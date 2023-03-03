<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateRangeToAdgroupFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('adgroup_feed','date_range')) {
            Schema::table('adgroup_feed', function (Blueprint $table) {
                $table->string('date_range')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adgroup_feed', function (Blueprint $table) {
            $table->dropColumn('date_range');
        });
    }
}
