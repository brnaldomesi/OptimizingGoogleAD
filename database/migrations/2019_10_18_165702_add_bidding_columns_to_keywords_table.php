<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBiddingColumnsToKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->char('bidding_strategy_id')->nullable();
            $table->char('bidding_strategy_name')->nullable();
            $table->char('bidding_strategy_source')->nullable();
            $table->char('bidding_strategy_type')->nullable();
            $table->char('cpc_bid_source')->nullable();
            $table->decimal('cpc_bid')->nullable();
            $table->decimal('cpm_bid')->nullable();
            $table->decimal('original_cpc_bid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('bidding_strategy_id');
            $table->dropColumn('bidding_strategy_name');
            $table->dropColumn('bidding_strategy_source');
            $table->dropColumn('bidding_strategy_type');
            $table->dropColumn('cpc_bid_source');
            $table->dropColumn('cpc_bid');
            $table->dropColumn('cpm_bid');
            $table->dropColumn('original_cpc_bid');
        });
    }
}
