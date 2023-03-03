<?php

namespace App\Console\Commands;

ini_set('max_execution_time', 10000);

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunPythonAccountsScript extends Command
{
    protected $signature = 'app:run-python-get-accounts';

    protected $description = 'Triggers python/every_time_user_registers.py';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        logger('running commmand app:run-python-get-accounts');
        $command_text = "python3 " . base_path() . "/python/every_time_user_registers.py";
        $command = escapeshellcmd($command_text);
        $output = shell_exec($command);
        logger($output); 

    }



}