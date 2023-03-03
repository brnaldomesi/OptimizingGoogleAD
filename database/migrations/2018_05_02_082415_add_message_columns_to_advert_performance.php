<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageColumnsToAdvertPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advert_performance', function (Blueprint $table) {
            $table->string('ctr_message')->nullable();
            $table->string('conversion_rate_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advert_performance', function (Blueprint $table) {
            $table->dropColumn('ctr_message');
            $table->dropColumn('conversion_rate_message');
        });
    }
}
