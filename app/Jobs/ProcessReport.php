<?php

namespace App\Jobs;

use App\Http\Service\Treatment;
use App\Report;
use http\Env\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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

        var_dump($this->data);
        if ($this->data['action'] == 'create') {
//            $report = new Report();
//            $report->git_repository = $this->data['git_repository'];
//            $report->filename = $filename;
//
//            $report->save();
        } else {
            $report = $this->data['report'];
        }

        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $report->users()->attach($data['user_id']);
        }
    }
}
