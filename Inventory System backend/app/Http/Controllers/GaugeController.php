<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gauge;
use App\Models\Incoming;
use App\Models\GlobalMethods;

class GaugeController extends Controller
{
    private $gm;

    public function __construct()
    {
        $this->gm = new GlobalMethods;
    }

    public function availableGauge(Request $request) {
        $incoming = Incoming::where('status','1')->take(1)->get();

        if($incoming->isEmpty()) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }
        else {
            $incoming = Incoming::where('status','1')->orderBy('id','DESC')->paginate($request->paginate);
            $result = $this->gm->funcPagination($incoming);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        }
    }

    public function searchAvailableGauge(Request $request) {
        $searchItem = $request->searchItem;

        $incoming = Incoming::where(function ($query) use($searchItem) {
            $query->where('gauge_type', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%');
        })
        ->where('status', 1)
        ->orderBy('id','DESC')
        ->paginate($request->paginate);

        if(count($incoming->items()) == 0) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }

        $result = $this->gm->funcPagination($incoming);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function requestedGauge(Request $request) {
        $gauge = Gauge::where('status','1')->take(1)->get();

        if($gauge->isEmpty()) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }
        else {
            $gauge = Gauge::where('status','1')->orderBy('date_requested','DESC')->paginate($request->paginate);
            $result = $this->gm->funcPagination($gauge);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        }
    }

    public function searchRequestedGauge(Request $request) {
        $searchItem = $request->searchItem;
        $searchByMonth = $request->searchByMonth;
        $searchByDate = $request->searchByDate;
        $isMonthAndDayOnly = $request->isMonthAndDayOnly;
        $isMonthAndDayOnlyValue = $request->isMonthAndDayOnlyValue;

        // 1st condition, filter by month eg. January - December
        if($searchByMonth >= 1 && $searchByMonth <= 12) {
            $gauge = Gauge::where(function ($query) use($searchByMonth, $searchItem) {
                $query->whereMonth('date_requested', $searchByMonth)
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%');
            })
            ->where('status', 1)
            ->orderBy('date_requested','DESC')
            ->paginate($request->paginate);
        }
        // 2nd condition, search if only month and day is input
        else if($isMonthAndDayOnly) {
            $gauge = Gauge::where(function ($query) use($isMonthAndDayOnlyValue) {
                $query->where('date_requested', $isMonthAndDayOnlyValue)
                ->orWhere('date_requested', 'LIKE', '%' .$isMonthAndDayOnlyValue. '%');
            })
            ->where('status', 1)
            ->orderBy('date_requested','DESC')
            ->paginate($request->paginate);
        }
        // 3rd condition filter by date eg. 2023-01-15
        else if($searchByDate != 'invalid') {
            $gauge = Gauge::where(function ($query) use($searchByDate, $searchItem) {
                $query->where('date_requested', 'LIKE', '%' .$searchByDate. '%')
                ->orWhere('date_requested', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('request_form_number', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('location', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%');
            })
            ->where('status', 1)
            ->orderBy('date_requested','DESC')
            ->paginate($request->paginate);
        }
        else {
            $gauge = Gauge::where(function ($query) use($searchItem) {
                $query->where('request_form_number', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('location', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('date_requested', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%');
            })
            ->where('status', 1)
            ->orderBy('date_requested','DESC')
            ->paginate($request->paginate);
        }

        if(count($gauge->items()) == 0) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }

        $result = $this->gm->funcPagination($gauge);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function returnedGauge(Request $request) {
        $gauge = Gauge::where('status','2')->take(1)->get();

        if($gauge->isEmpty()) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }
        else {
            $gauge = Gauge::selectRaw('
                id, request_form_number, gauge_type, size, serial_num, location, date_requested, 
                requested_by, location, returned_by, status, incoming_id, date_returned,
                CASE 
                    WHEN req_condition = 1 THEN "Okay"
                    WHEN req_condition = 3 THEN "Worn Out"
                    ELSE "Missing"
                END AS req_condition
            ')
            ->orderBy('date_returned','DESC')
            ->where('status', 2)
            ->paginate($request->paginate);

            $result = $this->gm->funcPagination($gauge);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        }
    }

    public function searchReturnedGauge(Request $request) {
        $searchItem = $request->searchItem;
        $searchByMonth = $request->searchByMonth;
        $searchByDate = $request->searchByDate;
        $isMonthAndDayOnly = $request->isMonthAndDayOnly;
        $isMonthAndDayOnlyValue = $request->isMonthAndDayOnlyValue;

        // 1st condition, filter by month eg. January - December
        if($searchByMonth >= 1 && $searchByMonth <= 12) {
            $gauge = Gauge::selectRaw('
                id, request_form_number, gauge_type, size, serial_num, location, date_requested, 
                requested_by, location, returned_by, status, incoming_id, date_returned,
                CASE 
                    WHEN req_condition = 1 THEN "Okay"
                    WHEN req_condition = 3 THEN "Worn Out"
                    ELSE "Missing"
                END AS req_condition
            ')->where(function ($query) use($searchItem, $searchByMonth, $isMonthAndDayOnlyValue) {
                $query->whereMonth('date_requested', $searchByMonth)
                ->orWhereMonth('date_returned', $searchByMonth)
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('returned_by', 'LIKE', '%' .$searchItem. '%');
            })
            ->orderBy('date_returned','DESC')
            ->where('status', 2)
            ->paginate($request->paginate);
        }
        // 2nd condition, search if only month and day is input
        else if($isMonthAndDayOnly) {
            $gauge = Gauge::selectRaw('
                id, request_form_number, gauge_type, size, serial_num, location, date_requested, 
                requested_by, location, returned_by, status, incoming_id, date_returned,
                CASE 
                    WHEN req_condition = 1 THEN "Okay"
                    WHEN req_condition = 3 THEN "Worn Out"
                    ELSE "Missing"
                END AS req_condition
            ')->where(function ($query) use($isMonthAndDayOnlyValue) {
                $query->where('date_requested', $isMonthAndDayOnlyValue)
                ->orWhere('date_returned', $isMonthAndDayOnlyValue)
                ->orWhere('date_requested', 'LIKE', '%' .$isMonthAndDayOnlyValue. '%')
                ->orWhere('date_returned', 'LIKE', '%' .$isMonthAndDayOnlyValue. '%');
            })
            ->orderBy('date_returned','DESC')
            ->where('status', 2)
            ->paginate($request->paginate);
        }
        // 3rd condition filter by date eg. 2023-01-15
        else if($searchByDate != 'invalid') {
            $gauge = Gauge::selectRaw('
                id, request_form_number, gauge_type, size, serial_num, location, date_requested, 
                requested_by, location, returned_by, status, incoming_id, date_returned,
                CASE 
                    WHEN req_condition = 1 THEN "Okay"
                    WHEN req_condition = 3 THEN "Worn Out"
                    ELSE "Missing"
                END AS req_condition
            ')->where(function ($query) use($searchItem, $searchByDate) {
                $query->where('date_requested', $searchByDate)
                ->orWhere('date_returned', $searchByDate)
                ->orWhere('date_requested', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('date_returned', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('request_form_number', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('location', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('returned_by', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('req_condition', 'LIKE', '%' .$searchItem. '%');
            })
            ->orderBy('date_returned','DESC')
            ->where('status', 2)
            ->paginate($request->paginate);
        }
        else {
            $req_condition = 0;
            if(
                strtolower($searchItem) == "missing" ||
                strtolower($searchItem) == "mis" ||
                strtolower($searchItem) == "miss" ||
                strtolower($searchItem) == "sing" ||
                strtolower($searchItem) == "worn out" ||
                strtolower($searchItem) == "worn" ||
                strtolower($searchItem) == "out" ||
                strtolower($searchItem) == "ok" ||
                strtolower($searchItem) == "okay"
            ) {
                if(strtolower($searchItem) == "okay" || strtolower($searchItem) == "ok") {
                    $req_condition = 1;
                }
                else if(strtolower($searchItem) == "worn out" || strtolower($searchItem) == "out" || strtolower($searchItem) == "worn") {
                    $req_condition = 3;
                }
                else if(strtolower($searchItem) == "missing" || strtolower($searchItem) == "mis" || strtolower($searchItem) == "miss" || strtolower($searchItem) == "sing") {
                    $req_condition = 4;
                }
            }

            $gauge = Gauge::selectRaw('
                id, request_form_number, gauge_type, size, serial_num, location, date_requested, 
                requested_by, location, returned_by, status, incoming_id, date_returned,
                CASE 
                    WHEN req_condition = 1 THEN "Okay"
                    WHEN req_condition = 3 THEN "Worn Out"
                    ELSE "Missing"
                END AS req_condition
            ')->where(function ($query) use($searchItem, $req_condition) {
                $query->where('request_form_number', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('location', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('date_requested', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('requested_by', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('date_returned', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('returned_by', 'LIKE', '%' .$searchItem. '%')
                ->orWhere('req_condition', $req_condition);
            })
            ->orderBy('date_returned','DESC')
            ->where('status', 2)
            ->paginate($request->paginate);
        }

        if(count($gauge->items()) == 0) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }

        $result = $this->gm->funcPagination($gauge);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function requestGauge(Request $request) {
        $this->validate($request, [
            'request_form_number' => 'required',
            'gauge_type' => 'required',
            'size' => 'required',
            'serial_num' => 'required',
            'date_requested' => 'required',
            'requested_by' => 'required',
            'location' => 'required',
        ]);

        try {
            $gauge = new Gauge();
            $gauge->request_form_number = $request->request_form_number;
            $gauge->gauge_type = $request->gauge_type;
            $gauge->size = $request->size;
            $gauge->serial_num = $request->serial_num;
            $gauge->date_requested = $request->date_requested;
            $gauge->requested_by = $request->requested_by;
            $gauge->location = $request->location;
            $gauge->incoming_id = $request->incoming_id;

            if($gauge->save()) {
                Incoming::where('id', $gauge->incoming_id)->update(['status' => 2]);
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 201, $request->gauge_type. ' has been requested.', $gauge),
                    'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
                ], 201);
            }
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', "false")
            ], 500);
        }
    }

    public function returnGauge(Request $request, $id) {
        $this->validate($request, [
            'date_returned' => 'required',
            'returned_by' => 'required',
            'condition' => 'required'
        ]);

        try {
            $gauge = Gauge::findOrFail($id);
            $gauge->date_returned = $request->date_returned;
            $gauge->returned_by = $request->returned_by;
            $gauge->req_condition = $request->condition;
            $gauge->status = 2;

            if($gauge->save()) {
                Incoming::where('id', $gauge->incoming_id)->update(['status' => $request->condition]);
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 200, 'Data has been returned.', $gauge),
                    'tokenInfo' => $this->gm->respondWithToken(auth()->refresh()),
                ], 200);
            }
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', "false")
            ], 500);
        }
    }

}
