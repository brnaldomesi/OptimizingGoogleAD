<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGraphDataToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->text('budget_target_graph_data')->nullable();
            $table->text('budget_actual_graph_data')->nullable();
            $table->text('kpi_target_graph_data')->nullable();
            $table->text('kpi_actual_graph_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('budget_target_graph_data');
            $table->dropColumn('budget_actual_graph_data');
            $table->dropColumn('kpi_target_graph_data');
            $table->dropColumn('kpi_actual_graph_data');
        });
    }
}
