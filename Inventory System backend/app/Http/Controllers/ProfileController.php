<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GlobalMethods;

class ProfileController extends Controller
{
    private $gm;

    public function __construct()
    {
        $this->gm = new GlobalMethods;
    }

    public function index() {
        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', auth()->user()),
            'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
        ], 200);
    }

    public function changeProfile(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required'
        ]);

        try {
            $user = User::findOrFail(auth()->user()->id);
            $user->name = $request->name;
            $user->username = $request->username;

            if($user->save()) {
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 200, 'Your profile has been updated.', $user),
                    'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
                ], 200);
            }
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Failed to update data.', $user)
            ], 500);
        }
    }

    public function changePassword(Request $request) {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => ['required','min:5']
        ]);

        $userPassword = auth()->user()->password;

        if(!app('hash')->check($request->current_password, $userPassword)) {
            return response()->json([   
                'apiResult' => $this->gm->apiResult('failed', 401, 'Password not matched!.', $request->all())
            ], 401);
        }

        try {
            $user = User::find(auth()->user()->id)->update(['password'=> app('hash')->make($request->new_password)]);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Your password has been changed.', $user),
                'tokenInfo' => $this->gm->respondWithToken(auth()->refresh())
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Please try again later or contact Administrator!', $user)
            ], 500);
        }
    }
}
