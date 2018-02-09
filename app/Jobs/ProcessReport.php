<?php

namespace App\Jobs;

use App\Http\Service\Treatment;
use App\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Response;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProcessReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param Report $report
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $treatment = new Treatment();
        $filename = $treatment->gitClone($this->data['git_repository']);

        if ($filename instanceof Response) {
            return;
        }

        if ($this->data['action'] == 'create') {
            $report = new Report();
            $report->git_repository = $this->data['git_repository'];
            $report->filename = $filename;

            $report->save();
        } else {
            $report = $this->data['report'];
        }

        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $report->users()->attach($data['user_id']);
        }

        Mail::send('welcome', ['filename' => $filename], function ($m) use ($filename) {
            $m->from('groupe3@asr.lan', 'Your Application');

            $m->to('groupe3@asr.lan')->subject('Your Reminder!');
            $m->attach(\Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . Treatment::DIR_FILE . '/' . $filename);
        });
    }
}