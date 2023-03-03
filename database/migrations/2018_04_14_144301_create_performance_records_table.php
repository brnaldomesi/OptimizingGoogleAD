<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_records', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();

            $table->char('recordable_id', 36);
            $table->text('recordable_type');

            $table->date('date')->nullable();

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
            $table->char('account_id', 36);

            $table->primary('id');
            $table->index('recordable_id');

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
        Schema::dropIfExists('performance_records');
    }
}
