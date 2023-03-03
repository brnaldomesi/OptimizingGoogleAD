<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGraphDataColumnsToAccountPerformanceChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_performance_changes', function (Blueprint $table) {
            $table->text('ctr_graph_data')->nullable();

            $table->text('conversion_rate_graph_data')->nullable();

            $table->text('cpa_graph_data')->nullable();

            $table->text('roas_graph_data')->nullable();

            $table->dropColumn('graph_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_performance_changes', function (Blueprint $table) {
            $table->dropColumn('ctr_graph_data');
            $table->dropColumn('conversion_rate_graph_data');
            $table->dropColumn('cpa_graph_data');
            $table->dropColumn('roas_graph_data');
            $table->text('graph_data')->nullable();
        });
    }
}
