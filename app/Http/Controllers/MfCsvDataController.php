<?php

namespace App\Http\Controllers;

use App\Models\Amc;
use App\Models\Category;
use App\Models\MutualFund;
use App\Models\MfDailyStat;
use App\Models\Sector;
use App\Models\Trustee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MfCsvDataController extends Controller
{
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv',
        ], [
            'csv_file.mimes'    => 'Only CSV files are allowed!',
            'csv_file.required' => 'Please select a CSV file to upload.',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $duplicates = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            fgetcsv($handle); // skip first row
            $header = fgetcsv($handle);
            $header = array_map(fn($h) => strtolower(str_replace(' ', '_', trim($h))), $header);

            $columns = array_flip($header);

            while (($row = fgetcsv($handle)) !== false) {
                $fundName = trim($row[$columns['fund']] ?? '');
                $amcName  = trim($row[$columns['amc']] ?? '');
                $validity_date = $this->parseDate($row[$columns['validity_date']] ?? null);

                if (!$fundName || !$amcName) continue;

                // AMC
                $amc = Amc::firstOrCreate(['name' => $amcName]);

                // Mutual Fund
                $mutualFund = MutualFund::firstOrCreate([
                    'name' => $fundName,
                    'amc_id' => $amc->id
                ]);

                // Sector
                $sectorName = trim($row[$columns['sector']] ?? '');
                $sector = $sectorName ? Sector::firstOrCreate(['name' => $sectorName]) : null;

                // Category
                $categoryName = trim($row[$columns['category']] ?? $row[$columns['category_name']] ?? '');

                $category = $categoryName ? Category::firstOrCreate(['name' => $categoryName]) : null;

                // Trustee
                $trusteeName = trim($row[$columns['trustee']] ?? '');
                $trustee = $trusteeName ? Trustee::firstOrCreate(['name' => $trusteeName]) : null;

                // Skip duplicates
                if (MfDailyStat::where('mutual_fund_id', $mutualFund->id)
                    ->where('category_id', $category?->id)
                    ->where('validity_date', $validity_date ?? null)
                    ->where('nav', $nav ?? null)
                    ->exists()
                ) {
                    $duplicates++;
                    continue;
                }

                // Insert record
                MfDailyStat::create([
                    'mutual_fund_id' => $mutualFund->id,
                    'amc_id'         => $amc->id,
                    'sector_id'      => $sector?->id,
                    'category_id'    => $category?->id,
                    'trustee_id'     => $trustee?->id,
                    'validity_date'  => $validity_date,
                    'inception_date' => $this->parseDate($row[$columns['inception_date']] ?? null),
                    'offer'          => $this->parseDecimal($row[$columns['offer']] ?? null),
                    'repurchase'     => $this->parseDecimal($row[$columns['repurchase']] ?? null),
                    'nav'            => $this->parseDecimal($row[$columns['nav']] ?? null),
                    'front_end'      => $this->parseDecimal($row[$columns['front-end']] ?? null),
                    'back_end'       => $this->parseDecimal($row[$columns['back-end']] ?? null),
                    'contingent'     => $this->parseDecimal($row[$columns['contingent']] ?? null),
                    'market'         => $row[$columns['market']] ?? null,
                ]);
            }

            fclose($handle);
        }

        $message = "CSV uploaded successfully!";
        if ($duplicates > 0) $message .= " Skipped $duplicates duplicate record(s).";

        return redirect()->back()->with('success', $message);
    }


    public function index(Request $request)
    {
        // 1. Prepare filter data
        $allFetchData = [
            'amcs'       => Amc::orderBy('name')->get(),
            'sectors'    => Sector::orderBy('name')->get(),
            'fundsList'  => MutualFund::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ];

        // 2. Build the query with relationships
        $fundsQuery = MfDailyStat::with(['amc', 'mutualFund', 'sector', 'trustee', 'category']);

        // 3. Apply filters if provided
        if ($request->filled('category')) $fundsQuery->where('category_id', $request->category);
        if ($request->filled('amc'))      $fundsQuery->where('amc_id', $request->amc);
        if ($request->filled('sector'))   $fundsQuery->where('sector_id', $request->sector);
        if ($request->filled('funds'))    $fundsQuery->where('mutual_fund_id', $request->funds);

        // ✅ Handle Date Filter (single date or date range)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fundsQuery->whereBetween('validity_date', [
                $request->from_date,
                $request->to_date
            ]);
        } elseif ($request->filled('from_date')) {
            $fundsQuery->where('validity_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $fundsQuery->where('validity_date', '<=', $request->to_date);
        }

        // 4. Paginate results
        $funds = $fundsQuery->orderBy('id', 'ASC')
            ->paginate($request->get('paged', 15))
            ->withQueryString();

        // 5. Return view
        return view('mufap.index', [
            'funds' => $funds,
            'allFetchData' => $allFetchData
        ]);
    }

    private function parseDate($date)
    {
        if (!$date) return null; // fallback default

        $formats = ['M d, Y', 'd-M-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, trim($date));
            if ($parsed) {
                return $parsed->format('Y-m-d');
            }
        }
        return null; // fallback if no format matched
    }


    private function parseDecimal($value)
    {
        return $value !== null && $value !== '' ? floatval(str_replace(',', '', $value)) : 0.0000;
    }
}
