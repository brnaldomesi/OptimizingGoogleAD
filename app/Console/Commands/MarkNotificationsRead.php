<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkNotificationsRead extends Command
{
    protected $signature = 'app:mark-notifications-read';

    protected $description = 'Marks notifcation read after 10 minutes';

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
        DB::table('notifications')
            ->where('read_at', null)
            ->where('created_at', '<', Carbon::now()->subMinutes(10))
            ->update(['read_at' => Carbon::now()]);
    }
}
