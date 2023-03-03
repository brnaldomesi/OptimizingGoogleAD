<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignWinningElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_winning_elements', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('campaign_id', 36);
            $table->char('type', 100)->nullable();
            $table->text('value')->nullable();

            $table->primary('id');
            $table->index('campaign_id');
            $table->char('account_id', 36);

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
        Schema::table('campaign_winning_elements', function (Blueprint $table) {
            Schema::dropIfExists('campaign_winning_elements');
        });
    }
}
