<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderAndDateRangeToAccountWinningElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_winning_elements', function (Blueprint $table) {
            $table->integer('order')->nullable();
            $table->char('date_range', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_winning_elements', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('date_range');
        });
    }
}
