<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTabColumnsToAdGroupFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adgroup_feed', function (Blueprint $table) {
            $table->boolean('read')->nullable();
            $table->boolean('archived')->nullable();
            $table->date('snooze_until_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adgroup_feed', function (Blueprint $table) {
            $table->dropColumn('read');
            $table->dropColumn('archived');
            $table->dropColumn('snooze_until_date');
        });
    }
}
