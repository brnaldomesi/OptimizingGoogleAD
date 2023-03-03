<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAdTestingColumnsFromAdGroupPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adgroup_performance', function (Blueprint $table) {
            if (Schema::hasColumn('adgroup_performance', 'message')) {
                $table->dropColumn('message');
            } 
            if (Schema::hasColumn('adgroup_performance', 'ad_count')) {
                $table->dropColumn('ad_count');
            } 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adgroup_performance', function (Blueprint $table) {
            //
        });
    }
}
