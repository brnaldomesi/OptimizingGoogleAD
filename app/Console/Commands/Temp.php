<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Console\Command;
use App\Libraries\AdWordsAPISession;
use App\Jobs\ProcessAdPerformanceReport;
use App\Jobs\GetAdPerformanceReportFromGoogle;

class Temp extends Command
{
    protected $signature = 'app:temp';

    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account = Account::first();

        $adWordsAPISession = new AdWordsAPISession($account->google_id, $account->user->refresh_token);

        $session = $adWordsAPISession->get();

        $start = Carbon::now()->subDays(1)->format('Ymd');

        $end = Carbon::now()->subDays(1)->format('Ymd');

        $fileName = 'app/reports/ad-performance/'.$account->id.'/'.$start.'.csv';

        echo 'downloading to '.getcwd().'/'.$fileName;

        GetAdPerformanceReportFromGoogle::dispatch($session, $fileName, $account, $start, $end);
    }
}
