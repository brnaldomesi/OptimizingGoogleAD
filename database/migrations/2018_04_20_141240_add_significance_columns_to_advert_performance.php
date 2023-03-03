<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignificanceColumnsToAdvertPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advert_performance', function (Blueprint $table) {
            $table->decimal('ctr_significance', 11, 1)->nullable();
            $table->decimal('conversion_rate_significance', 11, 1)->nullable();
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
            $table->dropColumn('ctr_significance');
            $table->dropColumn('conversion_rate_significance');
        });
    }
}
