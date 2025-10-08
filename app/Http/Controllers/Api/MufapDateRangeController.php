<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mf_Daily_Stats;
use Illuminate\Http\Request;

class MufapDateRangeController extends Controller
{
    public function getDateRange($id, Request $request)
    {
        //  Validate query parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        //  Fetch data from mf_daily_stats table
        $dateRangeData = Mf_Daily_Stats::where('mutual_fund_id', $id)
            ->whereBetween('validity_date', [$startDate, $endDate])
            ->orderBy('validity_date', 'asc')
            ->get(['validity_date as date', 'nav', 'offer', 'repurchase', 'market']);

        //  If no data found
        if ($dateRangeData->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No data found for the given date range.'
            ], 404);
        }

        // Return success response
        return response()->json([
            'status' => 'success',
            'data'   => $dateRangeData
        ], 200);
    }
}
