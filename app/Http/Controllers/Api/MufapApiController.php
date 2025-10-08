<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amc;
use App\Models\Category;
use App\Models\Mf_Daily_Stats;
use App\Models\Sector;
use Illuminate\Http\Request;



class MufapApiController extends Controller
{
    // Get all Mutual Funds with relations
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $data = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee', 'category'])
            ->orderBy('id', 'ASC')
            ->paginate($perPage);

        $data->getCollection()->transform(function ($item) {
            if (!empty($item->validity_date)) {
                $item->validity_date = \Carbon\Carbon::parse($item->validity_date)->format('d-M-Y');
            }
            if (!empty($item->inception_date)) {
                $item->inception_date = \Carbon\Carbon::parse($item->inception_date)->format('d-M-Y');
            }
            return $item;
        });

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
            return response()->json([
                'title'  => 'Service Temporarily Unavailable',
                'detail' => 'The requested resource is currently unavailable. Please try again later.',
                'status' => 503,
                'type'   => 'https://mufap.example.com/api/errors/service-unavailable'
            ], 503);
        }

        return response()->json([
            'status' => 'success',
            'data' => $record
        ], 200);
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

    /**
     * ✅ Get all AMCs (with related funds count)
     */
    public function getAllAMCs()
    {
        $data = Amc::withCount('mfDailyStats') // counts related funds
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();
        // dd($data->toArray());
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * ✅ Get all Categories (with related funds count)
     */
    public function getAllCategories()
    {
        $data = Category::withCount('mfDailyStats')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * ✅ Get all Sectors (with related funds count)
     */
    public function getAllSectors()
    {
        $data = Sector::withCount('mfDailyStats')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    // Filter by Date
    // public function filterByDate(Request $request)
    // {
    //     $date = $request->query('date');

    //     $query = Mf_Daily_Stats::with(['sector', 'amc', 'mutualFund', 'trustee']);

    //     if ($date) {
    //         $query->whereDate('validity_date', $date);
    //     }

    //     $data = $query->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $data
    //     ]);
    // }
}
