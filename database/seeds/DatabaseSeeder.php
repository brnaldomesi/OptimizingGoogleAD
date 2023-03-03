<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AccountsTableSeeder::class);
        $this->call(BudgetCommanderTableSeeder::class);
        $this->call(CampaignsTableSeeder::class);
        // $this->call(AdgroupsTableSeeder::class);
        // $this->call(AdvertsTableSeeder::class);
        $this->call(AccountPerformanceTableSeeder::class);
        $this->call(AccountPerformanceReportsTableSeeder::class);
        $this->call(CampaignPerformanceTableSeeder::class);
        // $this->call(AdgroupPerformanceTableSeeder::class);
        // $this->call(AdvertPerformanceTableSeeder::class);
        // $this->call(AccountWinningElementsTableSeeder::class);
        // $this->call(CampaignWinningElementsTableSeeder::class);
        // $this->call(AdgroupWinningElementsTableSeeder::class);
        // $this->call(BestPerfomersTableSeeder::class);
        // $this->call(WorstPerfomersTableSeeder::class);
        // $this->call(PotentialGainsTableSeeder::class);
        $this->call(AccountPerformanceChangesTableSeeder::class);
        // $this->call(AdNGramPerformanceTableSeeder::class);
        // $this->call(AdNGramFeedTableSeeder::class);
        // $this->call(AdgroupFeedTableSeeder::class);
        // $this->call(AdvertFeedTableSeeder::class);
        // $this->call(KeywordTableSeeder::class);
        // $this->call(KeywordPerformanceTableSeeder::class);
        // $this->call(KeywordFeedTableSeeder::class);
        // $this->call(SearchQueryNGramPerformanceTableSeeder::class);
        // $this->call(SearchQueryNGramFeedTableSeeder::class);
        // $this->call(SearchQueriesTableSeeder::class);
        // $this->call(SearchQueryPerformanceTableSeeder::class);
        // $this->call(SearchQueryFeedTableSeeder::class);
        $this->call(PlansTableSeeder::class);
    }
}
