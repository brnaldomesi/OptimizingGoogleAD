<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Libraries\AdPerformanceReportProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdPerformanceReportProcessorTest extends TestCase
{
    /** @test **/
    public function records_are_updated()
    {
        $account = factory(\App\Models\Account::class)->create();

        $rows = [
            [
                'Day' => '2018-05-11',
                'Ad ID' => '1232131',
                'Campaign ID' => '1231323',
                'Campaign' => 'Camaign name',
                'Campaign state' => 'ENABLED',
                'Ad group ID' => '3475739479',
                'Ad group' => 'Ad group name',
                'Ad group state' => 'ENABLED',
                'Ad ID' => '38475938475',
                'Headline 1' => 'Headline 1',
                'Headline 2' => 'Headline 2',
                'Description' => 'Description',
                'Path 1' => 'path1',
                'Path 2' => 'path2',
                'Ad state' => 'ENABLED',
                'Final URL' => '"[""http://www.example.co.uk/""]"',
                'Impressions' => '1000',
                'Clicks' => '100',
                'Conversions' => '10',
                'Avg. position' => '1.4',
                'Cost' => '1000000',
                'Total conv. value' => '2000000',
            ],
        ];
        (new AdPerformanceReportProcessor($account, $rows))->handle();

        $data = [
            'date'				=> '2018-05-11',
            'advert_google_id'	=>	'38475938475',
            'conversions'		=>	10,
        ];

        $this->assertDatabaseHas('ad_performance_reports', $data);

        //the next day's report has slightly different data because of delays in reporting
        $rows = [
            [
                'Day' => '2018-05-11',
                'Ad ID' => '1232131',
                'Campaign ID' => '1231323',
                'Campaign' => 'Camaign name',
                'Campaign state' => 'ENABLED',
                'Ad group ID' => '3475739479',
                'Ad group' => 'Ad group name',
                'Ad group state' => 'ENABLED',
                'Ad ID' => '38475938475',
                'Headline 1' => 'Headline 1',
                'Headline 2' => 'Headline 2',
                'Description' => 'Description',
                'Path 1' => 'path1',
                'Path 2' => 'path2',
                'Ad state' => 'ENABLED',
                'Final URL' => '"[""http://www.example.co.uk/""]"',
                'Impressions' => '1000',
                'Clicks' => '100',
                'Conversions' => '11',
                'Avg. position' => '1.4',
                'Cost' => '1000000',
                'Total conv. value' => '2000000',
            ],
        ];
        (new AdPerformanceReportProcessor($account, $rows))->handle();

        $data = [
            'date'				=> '2018-05-11',
            'advert_google_id'	=>	'38475938475',
            'conversions'		=>	10,
        ];

        //shouldn't see old data
        $this->assertDatabaseMissing('ad_performance_reports', $data);

        $data = [
            'date'				=> '2018-05-11',
            'advert_google_id'	=>	'38475938475',
            'conversions'		=>	11,
        ];

        //but should see the updated data
        $this->assertDatabaseHas('ad_performance_reports', $data);
    }
}
