<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBudgetAndKpiColumnsToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->decimal('budget', 11, 2)->nullable();
            $table->string('kpi_name')->nullable();
            $table->decimal('kpi_value', 11, 2)->nullable();
            $table->decimal('elapsed_time', 11, 2)->nullable();
            $table->decimal('budget_actual_vs_target', 11, 2)->nullable();
            $table->decimal('budget_forecast_vs_target', 11, 2)->nullable();
            $table->decimal('kpi_actual_vs_target', 11, 2)->nullable();
            $table->decimal('kpi_forecast_vs_target', 11, 2)->nullable();
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
            $table->dropColumn('budget');
            $table->dropColumn('kpi_name');
            $table->dropColumn('kpi_value');
            $table->dropColumn('elapsed_time');
            $table->dropColumn('budget_actual_vs_target');
            $table->dropColumn('budget_forecast_vs_target');
            $table->dropColumn('kpi_actual_vs_target');
            $table->dropColumn('kpi_forecast_vs_target');
        });
    }
}
