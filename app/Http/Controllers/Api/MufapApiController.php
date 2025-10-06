<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mf_Daily_Stats;
use Illuminate\Http\Request;


class MufapApiController extends Controller
{
    // Get all Mutual Funds with relations
    public function index()
    {
        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Get single Mutual Fund record by ID
    public function show($id)
    {
        $record = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])->find($id);

        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $record
        ]);
    }

    // Store new record
    public function store(Request $request)
    {
        $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'amc_id' => 'required|exists:amcs,id',
            'mutual_fund_id' => 'required|exists:mutual_funds,id',
            'nav' => 'nullable|numeric',
            'offer' => 'nullable|numeric',
            'repurchase' => 'nullable|numeric',
            'validity_date' => 'required|date',
        ]);

        $record = Mf_Daily_Stats::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Fund record created successfully',
            'data' => $record
        ], 201);
    }

    // Update record
    public function update(Request $request, $id)
    {
        $record = Mf_Daily_Stats::find($id);

        if (!$record) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Record not found'
                ],
                404
            );
        }

        $record->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Fund record updated successfully',
            'data' => $record
        ]);
    }

    // Delete record
    public function destroy($id)
    {
        $record = Mf_Daily_Stats::find($id);

        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
        }

        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Record deleted successfully'
        ]);
    }

    // Filter by AMC
    public function filterByAMC($amcId)
    {
        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])
            ->where('amc_id', $amcId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Filter by Sector
    public function filterBySector($sectorId)
    {
        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])
            ->where('sector_id', $sectorId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Filter by Date
    public function filterByDate($date)
    {
        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])
            ->where('validity_date', $date)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    // filter by category
    public function filterByCategory($categoryId)
    {
        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee'])
            ->where('category_id', $categoryId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
