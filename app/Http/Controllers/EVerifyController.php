<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class EVerifyController extends Controller
{
    private function accessToken(): string
    {
        if ($t = Cache::get('everify_token'))
            return $t;

        $data = Http::asForm()
            ->post(config('everify.auth_url'), [
                'client_id' => config('everify.client_id'),
                'client_secret' => config('everify.client_secret'),
            ])->throw()->json('data');

        Cache::put(
            'everify_token',
            $data['access_token'],
            now()->setTimestamp($data['expires_at'])->subSeconds(60)
        );

        return $data['access_token'];
    }

    public function verify(Request $r)
    {
        $v = $r->validate([
            'mode' => 'required|in:qr,manual',
            'session_id' => 'required|string',
            'value' => 'required_if:mode,qr|string',
            'first_name' => 'required_if:mode,manual|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required_if:mode,manual|string',
            'suffix' => 'nullable|string',
            'birth_date' => 'required_if:mode,manual|string',
        ]);

        $payload = $v['mode'] === 'qr'
            ? ['value' => trim($v['value']), 'face_liveness_session_id' => $v['session_id']]
            : [
                'first_name' => trim($v['first_name']),
                'middle_name' => trim($v['middle_name'] ?? ''),
                'last_name' => trim($v['last_name']),
                'suffix' => trim($v['suffix'] ?? ''),
                'birth_date' => $v['birth_date'],
                'face_liveness_session_id' => $v['session_id'],
            ];

        $url = $v['mode'] === 'qr' ? config('everify.qr_url') : config('everify.query_url');

        $res = Http::withToken($this->accessToken())->post($url, $payload);

        if (!$res->successful()) {
            Log::warning('eVerify failed', ['status' => $res->status(), 'body' => $res->body()]);
            return response()->json(['message' => 'Verification failed.'], 422);
        }

        $d = $res->json('data');
        $m = $res->json('meta');

        // Sanitized: only what the front end needs
        return response()->json([
            'code' => $d['code'] ?? null,
            'reference' => $d['reference'] ?? null,
            'first_name' => $d['first_name'] ?? null,
            'middle_name' => $d['middle_name'] ?? null,
            'last_name' => $d['last_name'] ?? null,
            'suffix' => $d['suffix'] ?? null,
            'gender' => $d['gender'] ?? null,
            'birth_date' => $d['birth_date'] ?? null,
            'place_of_birth' => $d['place_of_birth'] ?? null,
            'full_address' => $d['full_address'] ?? null,
            'present_full_address' => $d['present_full_address'] ?? null,
            'residency_status' => $d['residency_status'] ?? null,
            'marital_status' => $d['marital_status'] ?? null,
            'mobile_number' => $d['mobile_number'] ?? null,
            'face_url' => $d['face_url'] ?? null,
         
        ]);

    
    }
}