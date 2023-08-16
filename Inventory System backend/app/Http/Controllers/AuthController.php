<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GlobalMethods;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $gm;
    private $user;

    public function __construct()
    {
        $this->gm = new GlobalMethods;
        $this->user = new User;
    }

    public function login(Request $request) {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = request(['username', 'password']);
        $token = auth()->attempt($credentials);

        try {
            if (!$token) {
                return response()->json([
                    'apiResult' => $this->gm->apiResult('failed', 401, 'Wrong username or password!', $credentials),
                    'tokenInfo' => $this->gm->respondWithToken($token)
                ], 401);
            }

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Login Successfully!', $credentials),
                'tokenInfo' => $this->gm->respondWithToken($token)
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Wrong username or password!', false)
            ], 500);
        }
    }

    public function register(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required',
            'password' => 'required|min:5',
            'question' => 'required',
            'answer' => 'required',
        ]);

        try {
            $this->user->name = $request->name;
            $this->user->username = $request->username;
            $this->user->password = app('hash')->make($request->password);
            $this->user->question = $request->question;
            $this->user->answer = $request->answer;

            if($this->user->save()) {
                return response()->json([
                    'apiResult' => $this->gm->apiResult('success', 200, 'Account has been registered!', $this->user)
                ], 200);
            }
            
            // if($this->user->save()) {
            //     return $this->login($request);
            // }
        } 
        catch (\Exception $e) {
            return response()->json([
                'apiResult' => $this->gm->apiResult('failed', 500, $e->getMessage(), $this->user),
                'tokenInfo' => $this->gm->respondWithToken($token)
            ], 500);
        }
    }

    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    // this is for forgot password
    public function getUserData() {
        $result = DB::table('users')->select('*')->take(1)->get();

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
        ], 200);
    }

    public function forgotPassword(Request $request) {
        $this->validate($request, [
            'password' => ['required','min:5']
        ]);

        try {
            $user = User::where('id', $request->user_id)->update(['password' => app('hash')->make($request->password)]);

            return response()->json([
                'apiResult' => $this->gm->apiResult('success', 200, 'Your password has been changed.', $user)
            ], 200);
        } 
        catch (\Exception $e) {
            return response()->json([   
                'catchMessage' => $e->getMessage(),
                'apiResult' => $this->gm->apiResult('failed', 500, 'Failed to reset password.', false)
            ], 500);
        }
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Test Infinite Scroll
    public function testInfiniteScroll() {
        $result = DB::table('incomings')->paginate(15);

        return response()->json([
            'apiResult' => $this->gm->apiResult('success', 200, 'Successfully retrieved requested records', $result),
        ], 200);
    }
}
