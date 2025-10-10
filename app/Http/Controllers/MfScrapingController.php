<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Amc;
use App\Models\Category;
use App\Models\MutualFund;
use App\Models\MfDailyStat;
use App\Models\Sector;
use App\Models\Trustee;

class MfScrapingController extends Controller
{
    public function scrapeDaily($date)
    {
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD'], 422);
        }

        //  Check if date already exists in DB
        if (MfDailyStat::whereDate('validity_date', $date)->exists()) {
            return response()->json([
                'success' => false,
                'status' => 'skipped',
                'message' => "Data for {$date} already exists in the database. Skipping scrape.",
                'date' => $date
            ], 200);
        }

        $url = "https://mufap.com.pk/Industry/IndustryStatDaily?tab=3&AMCId=null&fundId=null&datefrom={$date}&datetill={$date}";

        // Step 1: Fetch HTML
        $response = Http::withoutVerifying()->get($url);
        if (!$response->ok() || empty($response->body())) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch MUFAP page or empty response.'], 500);
        }

        $html = $response->body();

        // Step 2: Parse HTML
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//tbody[contains(@class,"small")]/tr');

        // Step 3: If no rows found, return validation error
        if ($rows->length === 0) {
            return response()->json([
                'success' => false,
                'message' => "No data available on MUFAP for date {$date}",
                'url' => $url,
            ], 404);
        }

        $saved = 0;
        $duplicates = 0;

        foreach ($rows as $row) {
            $cols = [];
            foreach ($row->childNodes as $cell) {
                if ($cell->nodeName === 'td') {
                    $cols[] = $cell;
                }
            }

            if (count($cols) >= 14) {
                $record = [
                    'sector' => trim($cols[0]->textContent),
                    'amc' => trim($cols[1]->textContent),
                    'fund_name' => trim($cols[2]->textContent),
                    'category' => trim($cols[3]->textContent),
                    'inception_date' => $this->parseDate($cols[4]->textContent),
                    'nav' => $this->parseDecimal($cols[5]->textContent),
                    'offer' => $this->parseDecimal($cols[6]->textContent),
                    'repurchase' => $this->parseDecimal($cols[7]->textContent),
                    'validity_date' => $this->parseDate($cols[8]->textContent) ?? $date,
                    'front_end' => $this->parseDecimal($cols[9]->textContent),
                    'back_end' => $this->parseDecimal($cols[10]->textContent),
                    'contingent' => $this->parseDecimal($cols[11]->textContent),
                    'market' => trim($cols[12]->textContent),
                    'trustee' => trim($cols[13]->textContent),
                ];

                // Validate required fields
                if (empty($record['fund_name']) || empty($record['amc']) || empty($record['nav'])) {
                    continue; // skip incomplete rows
                }

                // Normalize & Save related models
                $amc = Amc::firstOrCreate(['name' => $record['amc']]);
                $sector = Sector::firstOrCreate(['name' => $record['sector']]);
                $category = Category::firstOrCreate(['name' => $record['category']]);
                $trustee = Trustee::firstOrCreate(['name' => $record['trustee']]);
                $fund = MutualFund::firstOrCreate([
                    'name' => $record['fund_name'],
                    'amc_id' => $amc->id
                ]);

                // Check duplicates (per fund)
                $exists = MfDailyStat::where('mutual_fund_id', $fund->id)
                    ->where('validity_date', $record['validity_date'])
                    ->exists();

                if ($exists) {
                    $duplicates++;
                    continue;
                }

                // Save record
                MfDailyStat::create([
                    'mutual_fund_id' => $fund->id,
                    'amc_id' => $amc->id,
                    'sector_id' => $sector->id,
                    'category_id' => $category->id,
                    'trustee_id' => $trustee->id,
                    'validity_date' => $record['validity_date'],
                    'inception_date' => $record['inception_date'],
                    'offer' => $record['offer'],
                    'repurchase' => $record['repurchase'],
                    'nav' => $record['nav'],
                    'front_end' => $record['front_end'],
                    'back_end' => $record['back_end'],
                    'contingent' => $record['contingent'],
                    'market' => $record['market'],
                ]);

                $saved++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Scraped and saved data for {$date}",
            'total_saved' => $saved,
            'duplicates' => $duplicates,
            'url' => $url,
        ]);
    }

    private function parseDate($date)
    {
        if (!$date) return null;
        $formats = ['M d, Y', 'd-M-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, trim($date));
            if ($parsed) return $parsed->format('Y-m-d');
        }
        return null;
    }

    private function parseDecimal($value)
    {
        return $value !== null && $value !== '' ? floatval(str_replace(',', '', $value)) : 0.0000;
    }
}
