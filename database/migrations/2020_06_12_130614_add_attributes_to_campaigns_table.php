<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributesToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->uuid('budget_group_id')->nullable();
            $table->string('ad_network_type_1')->nullable();
            $table->string('ad_network_type_2')->nullable();
            $table->string('advertising_channel_sub_type')->nullable();
            $table->string('advertising_channel_type')->nullable();
            $table->string('campaign_trial_type')->nullable();
            $table->string('bidding_strategy_type')->nullable();
            $table->string('bidding_strategy_name')->nullable();
            $table->string('bidding_strategy_id')->nullable();
            $table->string('budget_id')->nullable();
            $table->string('is_budget_explicitly_shared')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->decimal('campaign_desktop_bid_modifier')->nullable();
            $table->decimal('campaign_mobile_bid_modifier')->nullable();
            $table->decimal('campaign_tablet_bid_modifier')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('enhanced_cpc_enabled')->nullable();
            $table->string('final_url_suffix')->nullable();
            $table->string('has_recommended_budget')->nullable();
            $table->string('serving_status')->nullable();
            $table->string('url_custom_parameters')->nullable();
            $table->string('tracking_url_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('ad_network_type_1');
            $table->dropColumn('ad_network_type_2');
            $table->dropColumn('advertising_channel_sub_type');
            $table->dropColumn('advertising_channel_type');
            $table->dropColumn('campaign_trial_type');
            $table->dropColumn('bidding_strategy_type');
            $table->dropColumn('bidding_strategy_name');
            $table->dropColumn('bidding_strategy_id');
            $table->dropColumn('budget_id');
            $table->dropColumn('is_budget_explicitly_shared');
            $table->dropColumn('total_amount');
            $table->dropColumn('campaign_desktop_bid_modifier');
            $table->dropColumn('campaign_mobile_bid_modifier');
            $table->dropColumn('campaign_tablet_bid_modifier');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('enhanced_cpc_enabled');
            $table->dropColumn('final_url_suffix');
            $table->dropColumn('has_recommended_budget');
            $table->dropColumn('serving_status');
            $table->dropColumn('url_custom_parameters');
            $table->dropColumn('tracking_url_template');
        });
    }
}
