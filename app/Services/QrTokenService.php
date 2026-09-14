<?php

namespace App\Services;

use App\Models\AttendanceToken;
use App\Models\SchoolSetting;
use Illuminate\Support\Str;

class QrTokenService
{
    public function activeOrIssue(SchoolSetting $school): AttendanceToken
    {
        $active = AttendanceToken::query()
            ->where('school_setting_id', $school->id)
            ->where('active', true)
            ->latest('issued_at')->first();
        if ($active && $active->isValid()) {
            // re-expose plain token is not stored; need to re-issue if not available, so check plain_token attr
            // If no plain_token (after reload), rotate
            if ($active->getAttribute('plain_token')) return $active;
            // otherwise rotate to generate new raw token
        }
        return $this->issue($school);
    }

    public function issue(SchoolSetting $school): AttendanceToken
    {
        $now = now($school->timezone ?: config('attendance.timezone'));
        $rawToken = Str::random(48);

        AttendanceToken::query()
            ->where('school_setting_id', $school->id)
            ->where('active', true)
            ->update(['active' => false]);

        $token = AttendanceToken::query()->create([
            'school_setting_id' => $school->id,
            'token_hash' => hash('sha256', $rawToken),
            'issued_at' => $now,
            'expires_at' => $now->copy()->addSeconds(config('attendance.qr_ttl_seconds')),
            'active' => true,
        ]);

        $token->setAttribute('plain_token', $rawToken);

        return $token;
    }

    /** @return array<string, mixed> */
    public function payload(AttendanceToken $token): array
    {
        $rawToken = $token->getAttribute('plain_token');

        return [
            'id' => $token->id,
            'token' => $rawToken,
            'qr_url' => $this->qrUrl((string) $rawToken),
            'issued_at' => $token->issued_at?->toIso8601String(),
            'expires_at' => $token->expires_at?->toIso8601String(),
            'countdown_seconds' => max(0, now()->diffInSeconds($token->expires_at, false)),
        ];
    }

    private function qrUrl(string $rawToken): string
    {
        return rtrim(config('attendance.qr_image_endpoint'), '/') . '?' . http_build_query([
            'size' => '520x520',
            'data' => $rawToken,
            'qzone' => 2,
        ]);
    }
}
