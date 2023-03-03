<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeadline3Description2ToAdvertPerformanceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ad_performance_reports', function (Blueprint $table) {
            $table->string('headline_3')->nullable();
            $table->string('description_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ad_performance_reports', function (Blueprint $table) {
            $table->dropColumn('headline_3');
            $table->dropColumn('description_2');

        });
    }
}
