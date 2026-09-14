<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceResult;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Services\QrTokenService;
use App\Support\AttendancePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function __construct(private readonly QrTokenService $tokens)
    {
    }

    public function index(): View
    {
        $school = SchoolSetting::query()->firstOrFail();
        $token = $this->tokens->issue($school);

        return view('monitor.index', [
            'school' => $school,
            'activeQr' => $this->tokens->payload($token),
            'summary' => $this->summary($school),
        ]);
    }

    public function refresh(): JsonResponse
    {
        $school = SchoolSetting::query()->firstOrFail();
        $token = $this->tokens->issue($school);

        return response()->json([
            'ok' => true,
            'qr' => $this->tokens->payload($token),
        ]);
    }

    /** @return array<string, int|string> */
    private function summary(SchoolSetting $school): array
    {
        $today = now($school->timezone)->toDateString();
        $total = User::query()->where('role', UserRole::SISWA->value)->where('active', true)->count();
        $present = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->where('result', AttendanceResult::SUCCESS->value)
            ->whereHas('user', fn ($query) => $query->where('role', UserRole::SISWA->value))
            ->count();

        return [
            'present' => $present,
            'absent' => max(0, $total - $present),
            'percentage' => AttendancePresenter::percentage($present, $total),
        ];
    }
}
