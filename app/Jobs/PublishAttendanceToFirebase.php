<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\FirebaseAttendancePublisher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishAttendanceToFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Attendance $attendance)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseAttendancePublisher $publisher): void
    {
        $publisher->publish($this->attendance);
    }
}
