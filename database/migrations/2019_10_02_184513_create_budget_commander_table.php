<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBudgetCommanderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_commander', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->boolean('notify_via_email')->default(0);
            $table->boolean('pause_campaigns')->default(0);
            $table->boolean('enable_campaigns')->default(0);
            $table->boolean('rollover_spend')->default(0);
            $table->boolean('control_spend')->default(0);
            $table->boolean('email_sent')->default(0);
            $table->char('email_addresses')->nullable();
            $table->char('day_paused_campaigns')->nullable();
            $table->char('month_paused_campaigns')->nullable();
            $table->float('excess_budget')->nullable();
            $table->primary('id');
            $table->index('account_id');
            $table->engine = 'InnoDB';
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
            Schema::dropIfExists('budget_commander');
        });
    }
}
