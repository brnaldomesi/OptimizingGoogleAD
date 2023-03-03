<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordPerformanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyword_performance_reports', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->date('date')->nullable();

            $table->string('campaign_google_id')->nullable();
            $table->string('campaign_name');

            $table->string('campaign_status');
            $table->string('adgroup_google_id');
            $table->string('adgroup_name');
            $table->string('adgroup_status');
            $table->string('keyword_google_id')->nullable();

            $table->integer('impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('average_position', 11, 1)->nullable();
            $table->decimal('cost', 11, 2)->nullable();
            $table->decimal('conversion_value', 11, 2)->nullable();

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
        Schema::dropIfExists('keyword_performance_reports');
    }
}
