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
use Carbon\Carbon;

class MfScrapingController extends Controller
{
    /**
     * Scrape MUFAP data for a date range (and optional mutual fund)
     */
    public function scrapeRange(Request $request)
    {
        //  Validate request
        $request->validate([
            'date_from' => 'required|date_format:Y-m-d',
            'date_to' => 'required|date_format:Y-m-d|after_or_equal:date_from',
            'mf_id' => 'nullable|integer|exists:mutual_funds,id',,
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);
        $mutualFundId = $request->mf_id ?? null;

        $totalSaved = 0;
        $totalSkipped = 0;
        $duplicateCount = 0;
        $allDates = [];

        //  Loop through each date in range
        for ($date = $dateFrom; $date->lte($dateTo); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $allDates[] = $formattedDate;

            //  Skip if data already exists
            if (MfDailyStat::whereDate('validity_date', $formattedDate)->exists()) {
                $totalSkipped++;
                continue;
            }

            //  Build MUFAP URL
            $fundParam = $mutualFundId
                ? MutualFund::find($mutualFundId)->id
                : 'null';

            $url = "https://mufap.com.pk/Industry/IndustryStatDaily";
            $params = [
                'tab' => 3,
                'AMCId' => 'null',
                'fundid' => $fundParam,
                'datefrom' => $formattedDate,
                'datetill' => $formattedDate,
            ];
            $response = Http::withoutVerifying()->get($url, $params);


            if ($response->failed() || empty($response->body())) {
                continue;
            }

            // 🧾 Parse HTML
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($response->body());
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);
            $rows = $xpath->query('//tbody[contains(@class,"small")]/tr');

            if (!$rows || $rows->length === 0) {
                continue;
            }

            foreach ($rows as $row) {
                $cols = [];
                foreach ($row->childNodes as $child) {
                    if ($child instanceof \DOMElement && $child->tagName === 'td') {
                        $cols[] = $child;
                    }
                }
                if (count($cols) < 14) continue;

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

                //  Resolve references
                $amc = Amc::firstOrCreate(['name' => $record['amc']]);
                $sector = Sector::firstOrCreate(['name' => $record['sector']]);
                $category = Category::firstOrCreate(['name' => $record['category']]);
                $trustee = Trustee::firstOrCreate(['name' => $record['trustee']]);

                $fund = MutualFund::firstOrCreate(
                    ['name' => $record['fund_name']],
                    [
                        'amc_id' => $amc->id,
                        'sector_id' => $sector->id,
                        'category_id' => $category->id,
                        'trustee_id' => $trustee->id,
                        'inception_date' => $record['inception_date'],
                    ]
                );

                //  Check duplicate fund-date combo
                $exists = MfDailyStat::where('mutual_fund_id', $fund->id)
                    ->where('validity_date', $record['validity_date'])
                    ->exists();

                if ($exists) {
                    $duplicateCount++;
                    continue;
                }

                // 💾 Save new record
                MfDailyStat::create([
                    'mutual_fund_id' => $fund->id,
                    'amc_id'         => $amc->id,
                    'sector_id'      => $sector->id,
                    'category_id'    => $category->id,
                    'trustee_id'     => $trustee->id,
                    'validity_date'  => $record['validity_date'],
                    'inception_date' => $record['inception_date'],
                    'offer'          => $record['offer'],
                    'repurchase'     => $record['repurchase'],
                    'nav'            => $record['nav'],
                    'front_end'      => $record['front_end'],
                    'back_end'       => $record['back_end'],
                    'contingent'     => $record['contingent'],
                    'market'         => $record['market'],
                ]);

                $totalSaved++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Scraping completed successfully.',
            'date_range' => [
                'from' => $dateFrom->format('Y-m-d'),
                'to' => $dateTo->format('Y-m-d'),
            ],
            'mutual_fund_id' => $mutualFundId,
            'total_saved' => $totalSaved,
            'duplicates' => $duplicateCount,
            'skipped_dates' => $totalSkipped,
            'processed_dates' => $allDates,
        ]);
    }

    //  Helper: parse date formats
    private function parseDate($value)
    {
        $value = trim($value);
        if (!$value) return null;

        $formats = ['M d, Y', 'd-M-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date) return $date->format('Y-m-d');
        }
        return null;
    }

    //  Helper: parse decimals
    private function parseDecimal($value)
    {
        $value = str_replace(',', '', trim($value));
        return is_numeric($value) ? (float) $value : null;
    }
}
