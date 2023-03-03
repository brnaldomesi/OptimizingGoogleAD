<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('adgroup_id', 36)->nullable();
            $table->string('google_id')->nullable();
            $table->string('headline_part_1')->nullable();
            $table->string('headline_part_2')->nullable();
            $table->string('description')->nullable();
            $table->string('path_1')->nullable();
            $table->string('path_2')->nullable();
            $table->string('status')->nullable();
            $table->text('final_urls')->nullable();
            $table->decimal('cost', 11, 2)->nullable();
            $table->integer('impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('total_conversion_value', 11, 2)->nullable();
            $table->boolean('loser')->nullable();
            $table->decimal('potential_savings', 11, 2)->nullable();
            $table->char('account_id', 36);

            $table->primary('id');
            $table->index('loser');
            $table->index('adgroup_id');

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
        Schema::dropIfExists('adverts');
    }
}
