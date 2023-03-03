<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdPerformanceReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_performance_reports', function (Blueprint $table) {
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
            $table->string('advert_google_id');
            $table->string('headline_1');
            $table->string('headline_2');
            $table->string('description');
            $table->string('path_1');
            $table->string('path_2');
            $table->string('advert_status');
            $table->text('final_urls');
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
        Schema::table('ad_performance_reports', function (Blueprint $table) {
            Schema::dropIfExists('ad_performance_reports');
        });
    }
}
