<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstimatedBidsToKeywords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->string('first_page_cpc')->nullable();
            $table->string('first_position_cpc')->nullable();
            $table->string('top_of_page_cpc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keywords', function (Blueprint $table) {
            $table->dropColumn('first_page_cpc');
            $table->dropColumn('first_position_cpc');
            $table->dropColumn('top_of_page_cpc');
        });
    }
}
