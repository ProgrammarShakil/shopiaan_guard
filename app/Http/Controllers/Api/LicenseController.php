<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseController extends Controller
{
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'ip_address' => 'required|ip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid license key'
            ], 404);
        }

        if (!$license->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'License is not active'
            ], 403);
        }

        if ($license->domain && $license->domain !== $request->domain) {
            return response()->json([
                'status' => 'error',
                'message' => 'License is already activated on another domain'
            ], 403);
        }

        if (!$license->domain) {
            if (!$license->canActivateDomain()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maximum number of domains reached'
                ], 403);
            }

            $license->activateDomain($request->domain, $request->ip_address);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'License is valid',
            'data' => [
                'license_type' => $license->license_type,
                'features' => $license->features,
                'expires_at' => $license->expires_at,
                'remaining_days' => $license->getRemainingDays(),
            ]
        ]);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $license = License::where('license_key', $request->license_key)
            ->where('domain', $request->domain)
            ->first();

        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid license key or domain'
            ], 404);
        }

        if (!$license->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'License is not active'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'License is valid',
            'data' => [
                'license_type' => $license->license_type,
                'features' => $license->features,
                'expires_at' => $license->expires_at,
                'remaining_days' => $license->getRemainingDays(),
            ]
        ]);
    }
} 