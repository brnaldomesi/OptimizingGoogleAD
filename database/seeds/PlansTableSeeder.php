<?php

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            ['name' => '1U1A-usd',
                'stripe_prod' => 'prod_Fw3h70EKa6WfX5',
                'stripe_plan' => 'plan_FwN0AdtuVsnEaP',
                'user_limit' => 1,
                'account_limit' => 1,
                'price' => 10000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
            ['name' => '1U5A-usd',
                'stripe_prod' => 'prod_FwNAHenwWGn7hA',
                'stripe_plan' => 'plan_FwNASfx9qNYYuI',
                'user_limit' => 1,
                'account_limit' => 5,
                'price' => 15000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
            ['name' => '1U10A-usd',
                'stripe_prod' => 'prod_FwNHEWmg4tz4DH',
                'stripe_plan' => 'plan_FwNIpnMYyK0TU3',
                'user_limit' => 1,
                'account_limit' => 10,
                'price' => 20000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
            ['name' => '1U20A-usd',
                'stripe_prod' => 'prod_FwNyEobBUBCByL',
                'stripe_plan' => 'plan_FwNzJS5jXc6vKn',
                'user_limit' => 1,
                'account_limit' => 20,
                'price' => 25000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
            ['name' => '1U50A-usd',
                'stripe_prod' => 'prod_FwO0GE3P1Q92lv',
                'stripe_plan' => 'plan_FwO0SdGON6BXY9',
                'user_limit' => 1,
                'account_limit' => 50,
                'price' => 30000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
            ['name' => '1U50+A-usd',
                'stripe_prod' => 'prod_FwO12s2TAf3pbH',
                'stripe_plan' => 'plan_FwO2My48R7Qpkz',
                'user_limit' => 1,
                'account_limit' => 500,
                'price' => 40000,
                'currency' => 'USD',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
//GBP
            ['name' => '1U1A-gbp',
                'stripe_prod' => 'prod_Fw3h70EKa6WfX5',
                'stripe_plan' => 'plan_FxUv2mynAU0JT7',
                'user_limit' => 1,
                'account_limit' => 1,
                'price' => 8000,
                'currency' => 'GBP',
                'frequency' => 'month',
                'active' => 1,
                'created_at'=> now()
            ],
        ]);
    }
}

