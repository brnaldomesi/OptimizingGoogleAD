<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBudgetGroupFieldsToBudgetCommander extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budget_commander', function (Blueprint $table) {
            $table->char('budget_group_name')->default('master')->after('account_id');
            $table->decimal('budget', 11, 2)->nullable()->after('budget_group_name');
            $table->string('kpi_name')->nullable()->after('budget_group_name');
            $table->decimal('kpi_value', 11, 2)->nullable()->after('budget_group_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budget_commander', function (Blueprint $table) {
            $table->dropColumn('budget_group_name');
            $table->dropColumn('budget');
            $table->dropColumn('kpi_name');
            $table->dropColumn('kpi_value');
        });
    }
}
