<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalMethods extends Model
{
    public function respondWithToken($token) {
        return [
            'user' => auth()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            // time expiration here is just a viewing, you can edit the time in vendor/tymon/jwt-auth/config/config.php
            // session expiration is set to 1 hr 
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    public function apiResult($status, $code, $message, $payload) {
        return [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'payload' => $payload
        ];
    }

    public function funcPagination($dt) {
        $result = [
            'links' => [
                'pagination' => [
                    'total' => $dt->total(),
                    'per_page' => $dt->perPage(),
                    'current_page' => $dt->currentPage(),
                    'last_page' => $dt->lastPage(),
                    'next_page_url' => $dt->nextPageUrl(),
                    'prev_page_url' => $dt->previousPageUrl(),
                    'from' => $dt->firstItem(),
                    'to' => $dt->lastItem(),
                ],
            ],
            'data' => $dt->items(),
        ];
        return $result;
    }
}
