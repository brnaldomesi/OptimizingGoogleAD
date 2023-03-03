<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdCountToAdgroupPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adgroup_performance', function (Blueprint $table) {
            $table->string('ad_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('adgroup_performance', 'ad_count')){
            Schema::table('adgroup_performance', function (Blueprint $table) {
                $table->dropColumn('ad_count');
            });
        }
    }
}
