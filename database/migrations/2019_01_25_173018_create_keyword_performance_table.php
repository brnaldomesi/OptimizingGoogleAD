<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyword_performance', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->char('keyword_id', 36);
            $table->char('date_range', 100)->nullable();
            $table->integer('impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('average_position', 11, 1)->nullable();
            $table->decimal('cost', 11, 2)->nullable();
            $table->decimal('average_cpc', 11, 2)->nullable();
            $table->decimal('conversion_value', 11, 2)->nullable();
            $table->decimal('cpa', 11, 2)->nullable();
            $table->decimal('roas', 11, 2)->nullable();
            $table->decimal('conversion_rate', 11, 2)->nullable();
            $table->decimal('ctr', 11, 2)->nullable();
            $table->primary('id');
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
        Schema::dropIfExists('keyword_performance');
    }
}
