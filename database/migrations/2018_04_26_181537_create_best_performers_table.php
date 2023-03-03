<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBestPerformersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('best_performers', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->char('advert_id', 36);
            $table->string('date_range');
            $table->decimal('ctr', 11, 2)->nullable();
            $table->decimal('cpa', 11, 2)->nullable();
            $table->decimal('roas', 11, 2)->nullable();
            $table->decimal('conversion_rate', 11, 2)->nullable();

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
        Schema::table('best_performers', function (Blueprint $table) {
            $table->dropIfExists('best_performers');
        });
    }
}
