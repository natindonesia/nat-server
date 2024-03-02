<?php

namespace App\Http\Controllers;

use App\Models\StateLog;
use Illuminate\Http\Request;

class StateLogController extends Controller
{

    public function store(Request $request)
    {
        if (config('auth.api_authorization') !== $request->header('Authorization')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // sensors must be an associative array with
        // device should be string
        $validate = $request->validate([
            'device' => 'required|string',
            'sensors' => 'required|array',
        ]);


        $stateLog = StateLog::create([
            'device_name' => $validate['device'],
            'ip_address' => $request->ip(),
            'headers' => $request->header(),
            'state' => $validate['sensors'],
        ]);

        return response()->json([
            'message' => 'State log created',
        ]);
    }
}
