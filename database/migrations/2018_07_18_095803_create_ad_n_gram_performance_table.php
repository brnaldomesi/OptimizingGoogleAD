<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdNgramPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_n_gram_performance', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->char('date_range', 100)->nullable();
            $table->string('n_gram')->nullable();
            $table->integer('n_gram_count')->nullable();
            $table->boolean('show_on_graph')->nullable();
            $table->decimal('ctr', 11, 2)->nullable();
            $table->decimal('ctr_significance', 11, 2)->nullable();

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

            $table->integer('graph_order')->nullable();
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
        Schema::dropIfExists('ad_n_gram_performance');
    }
}
