<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdTestingColumnsAdGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adgroups', function (Blueprint $table) {
            $table->string('priority')->nullable();
            $table->string('message')->nullable();
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
        Schema::table('adgroups', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->dropColumn('message');
            $table->dropColumn('ad_count');
        });
    }
}
