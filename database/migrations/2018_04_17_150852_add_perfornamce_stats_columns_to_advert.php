<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerfornamceStatsColumnsToAdvert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->decimal('average_position', 11, 1)->nullable();
            $table->decimal('average_cpc', 11, 2)->nullable();
            $table->decimal('cpa', 11, 2)->nullable();
            $table->decimal('roas', 11, 2)->nullable();
            $table->decimal('conversion_rate', 11, 2)->nullable();
            $table->decimal('ctr', 11, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropColumn('average_position');
            $table->dropColumn('average_cpc');
            $table->dropColumn('cpa');
            $table->dropColumn('roas');
            $table->dropColumn('conversion_rate');
            $table->dropColumn('ctr');
        });
    }
}
