<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mf_Daily_Stats;
use Illuminate\Http\Request;

class MufapDateRangeController extends Controller
{
    public function getDateRange($id, Request $request)
    {
        // Validate input
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        // Fetch data for date range
        $dateRangeData = Mf_Daily_Stats::where('mutual_fund_id', $id)
            ->whereBetween('validity_date', [$startDate, $endDate])
            ->orderBy('validity_date', 'asc')
            ->get(['validity_date as date', 'nav', 'offer', 'repurchase']);

        // Check if data exists
        if ($dateRangeData->isEmpty()) {
            return response()->json([
                'title'   => 'No Data Found',
                'status'  => 'error',
                'message' => 'No data found for the given date range.'
            ], 404);
        }

        // Calculate total days between range
        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;

        // Return formatted response
        return response()->json([
            'status'      => 'success',
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'total_days'  => $totalDays,
            'data'        => $dateRangeData,
        ], 200);
    }
}
