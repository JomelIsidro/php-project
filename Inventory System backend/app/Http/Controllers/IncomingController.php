<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incoming;
use App\Models\GlobalMethods;

class IncomingController extends Controller
{
    private $gm;

    public function __construct()
    {
        $this->gm = new GlobalMethods;
    }

    // this is for filtering the day, month and year through dropdown
    public function filterIncomingByDate(Request $request) {
        $month = $request->month;
        $day = $request->day;
        $year = $request->year;

        $dayMonthYear = '';

        if($request->month != '') {
            if($request->day != '') {
                if($request->year != '') {
                    $dayMonthYear .= "mdy";
                }
                else {
                    $dayMonthYear .= "md";
                }
            }
            else if($request->year != '') {
                $dayMonthYear .= "my";
            }
            else {
                $dayMonthYear .= "m";
            }
        }
        else if($request->day != '') {
            if($request->year != '') {
                $dayMonthYear .= "dy";
            }
            else {
                $dayMonthYear .= "d";
            }
        }
        else {
            $dayMonthYear .= "y";
        }

        // query of filtering by date
        if($dayMonthYear == "m") {
            $incoming = Incoming::whereMonth('date_received', $month)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "d") {
            $incoming = Incoming::whereDay('date_received', $day)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "y") {
            $incoming = Incoming::whereYear('date_received', $year)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "my") {
            $incoming = Incoming::whereMonth('date_received', $month)
            ->whereYear('date_received', $year)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "md") {
            $incoming = Incoming::whereMonth('date_received', $month)
            ->whereDay('date_received', $day)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "dy") {
            $incoming = Incoming::whereDay('date_received', $day)
            ->whereYear('date_received', $year)
            ->paginate($request->paginate);
        }
        else if($dayMonthYear == "mdy") {
            $incoming = Incoming::whereMonth('date_received', $month)
            ->whereDay('date_received', $day)
            ->whereYear('date_received', $year)
            ->paginate($request->paginate);
        }

        if(count($incoming->items()) == 0) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }

        $result = $this->gm->funcPagination($incoming);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
        ], 200);
    }

    public function index(Request $request) {
        $incoming = Incoming::take(1)->get();
        // $incoming = Incoming::where('status','5')->take(1)->get();

        if($incoming->isEmpty()) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $incoming),
            ], 200);
        }
        else {
            $incoming = Incoming::orderBy('id','DESC')->paginate($request->paginate);
            $result = $this->gm->funcPagination($incoming);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        }
    }

    public function search(Request $request) {
        $searchItem = $request->searchItem;
        $searchByMonth = $request->searchByMonth;
        $searchByDate = $request->searchByDate;
        $isMonthAndDayOnly = $request->isMonthAndDayOnly;
        $isMonthAndDayOnlyValue = $request->isMonthAndDayOnlyValue;

        // 1st condition, filter by month eg. January - December
        if($searchByMonth >= 1 && $searchByMonth <= 12) {
            $incoming = Incoming::whereMonth('date_received', $searchByMonth)
            ->orWhere('maker', 'LIKE', '%' .$searchItem. '%')
            ->orderBy('id','DESC')
            ->paginate($request->paginate);
        }
        // 2nd condition, search if only month and day is input eg. feb 12
        else if($isMonthAndDayOnly) {
            $incoming = Incoming::where('date_received', $isMonthAndDayOnlyValue)
            ->orderBy('id','DESC')
            ->paginate($request->paginate);
        }
        // 3rd condition filter by date eg. 2023-01-15
        else if($searchByDate != 'invalid') {
            $incoming = Incoming::where('date_received', $searchByDate)
            ->orWhere('date_received', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('slip_num', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('maker', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
            ->orderBy('id','DESC')
            ->paginate($request->paginate);
        }
        else {
            $status = 0;
            if(
                strtolower($searchItem) == "request" ||
                strtolower($searchItem) == "requested" ||
                strtolower($searchItem) == "missing" ||
                strtolower($searchItem) == "mis" ||
                strtolower($searchItem) == "miss" ||
                strtolower($searchItem) == "sing" ||
                strtolower($searchItem) == "worn out" ||
                strtolower($searchItem) == "worn" ||
                strtolower($searchItem) == "out"
            ) {
                if(strtolower($searchItem) == "request" || strtolower($searchItem) == "requested") {
                    $status = 2;
                }
                else if(strtolower($searchItem) == "worn out" || strtolower($searchItem) == "out" || strtolower($searchItem) == "worn") {
                    $status = 3;
                }
                else if(strtolower($searchItem) == "missing" || strtolower($searchItem) == "mis" || strtolower($searchItem) == "miss" || strtolower($searchItem) == "sing") {
                    $status = 4;
                }
            }

            $incoming = Incoming::where('slip_num', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('date_received', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('maker', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('size', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('serial_num', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('status', $status)
            ->orderBy('id','DESC')
            ->paginate($request->paginate);
        }

        if(count($incoming->items()) == 0) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', [])
            ], 200);
        }

        $result = $this->gm->funcPagination($incoming);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function show($id) {
        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested single record', Incoming::where('id', $id)->get()),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'gauge_type' => 'required',
            'maker' => 'required',
            'size' => 'required',
            'serial_num' => 'required',
        ]);

        try {
            $incoming = new Incoming();
            $incoming->slip_num = $request->slip_num;
            $incoming->date_received = $request->date_received;
            $incoming->gauge_type = $request->gauge_type;
            $incoming->maker = $request->maker;
            $incoming->size = $request->size;
            $incoming->serial_num = $request->serial_num;

            if($incoming->save()) {
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 201, 'New Data has been added.', $incoming),
                    'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
                ], 201);
            }
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', $incoming)
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'gauge_type' => 'required',
            'maker' => 'required',
            'size' => 'required',
            'serial_num' => 'required',
        ]);

        try {
            $incoming = Incoming::findOrFail($id);
            $incoming->slip_num = $request->slip_num;
            $incoming->date_received = $request->date_received;
            $incoming->gauge_type = $request->gauge_type;
            $incoming->maker = $request->maker;
            $incoming->size = $request->size;
            $incoming->serial_num = $request->serial_num;

            if($incoming->save()) {
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 200, 'Data has been updated.', $incoming),
                    'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
                ], 200);
            }
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', $incoming)
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $incoming = Incoming::where('id', $id)->delete();

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Data has been deleted.', $incoming),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        } catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', $incoming)
            ], 500);
        }
    }

    public function stocks(Request $request) {

        $incoming = Incoming::where('status','1')->take(1)->get();

        if($incoming->isEmpty()) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', []),
            ], 200);
        }
        else {
            $incoming = Incoming::selectRaw('size, gauge_type, count(size) as qty')
            ->orderBy('size','asc')
            ->groupBy('size','gauge_type')
            ->where('status',1)
            ->paginate($request->paginate);

            $result = $this->gm->funcPagination($incoming);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        }
    }

    public function searchStocks(Request $request) {
        $searchItem = $request->searchItem;

        $incoming = Incoming::selectRaw('size, gauge_type, count(size) as qty')
            ->where(function ($query) use($searchItem) {
            $query->where('size', 'LIKE', '%' .$searchItem. '%')
            ->orWhere('gauge_type', 'LIKE', '%' .$searchItem. '%');
        })
        ->orderBy('size','asc')
        ->groupBy('size','gauge_type')
        ->where('status', 1)
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

}
