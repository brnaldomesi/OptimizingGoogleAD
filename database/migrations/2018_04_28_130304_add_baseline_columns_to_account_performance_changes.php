<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBaselineColumnsToAccountPerformanceChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_performance_changes', function (Blueprint $table) {
            $table->decimal('ctr_baseline', 11, 2)->nullable();
            $table->decimal('conversion_rate_baseline', 11, 2)->nullable();
            $table->decimal('cpa_baseline', 11, 2)->nullable();
            $table->decimal('roas_baseline', 11, 2)->nullable();
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
            $table->dropColumn('ctr_baseline');
            $table->dropColumn('conversion_rate_baseline');
            $table->dropColumn('cpa_baseline');
            $table->dropColumn('roas_baseline');
        });
    }
}
