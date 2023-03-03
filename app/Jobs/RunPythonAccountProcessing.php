<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunPythonAccountProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $args;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($args)
    {
      $this->args = $args;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $args = $this->args;
        logger('running job RunPythonAccountProcessing');
        $command_text = "python3 " . base_path() . "/python/process_account.py -a " . $args;
        $command = escapeshellcmd($command_text);
        $output = shell_exec($command);
        logger($command); 
        logger($output); 
        logger($args);
    }
}