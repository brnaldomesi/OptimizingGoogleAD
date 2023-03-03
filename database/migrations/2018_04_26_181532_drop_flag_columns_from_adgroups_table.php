<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFlagColumnsFromAdgroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adgroups', function (Blueprint $table) {
            $table->dropColumn('has_winners');
            $table->dropColumn('too_many_adverts');
            $table->dropColumn('too_few_adverts');
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
            $table->boolean('has_winners')->nullable();
            $table->boolean('too_many_adverts')->nullable();
            $table->boolean('too_few_adverts')->nullable();
        });
    }
}
