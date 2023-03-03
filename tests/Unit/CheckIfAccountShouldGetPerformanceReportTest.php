<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Symfony\Component\Process\Process;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Libraries\CheckIfAccountShouldGetAdPerformanceReport;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CheckIfAccountShouldGetPerformanceReportTest extends TestCase
{
    /** @test **/
    public function false_if_before_4am()
    {

        /*
        $account = factory(\App\Models\Account::class)->make([

        ]);
        */
    }
}
