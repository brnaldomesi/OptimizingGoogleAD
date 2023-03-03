<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdNGramPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Account::all() as $account) {
            factory(App\Models\AdNGramPerformance::class, 9)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_14_days',
                'show_on_graph'	=>	false,
            ]);

            //now make 3 to show on the graph
            factory(App\Models\AdNGramPerformance::class)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_14_days',
                'show_on_graph'	=>	true,
                'graph_order'	=>	1,
            ]);

            factory(App\Models\AdNGramPerformance::class)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_14_days',
                'show_on_graph'	=>	true,
                'graph_order'	=>	2,
            ]);

            factory(App\Models\AdNGramPerformance::class)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_14_days',
                'show_on_graph'	=>	true,
                'graph_order'	=>	3,
            ]);
        }
    }
}
