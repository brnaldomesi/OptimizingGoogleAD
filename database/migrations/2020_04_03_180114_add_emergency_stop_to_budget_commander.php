<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmergencyStopToBudgetCommander extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budget_commander', function (Blueprint $table) {
          $table->boolean('emergency_stop')->nullable()->default(0);
          $table->longText('day_paused_campaigns')->change();
          $table->longText('month_paused_campaigns')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budget_commander', function (Blueprint $table) {
          $table->dropColumn('emergency_stop');
        });
    }
}
