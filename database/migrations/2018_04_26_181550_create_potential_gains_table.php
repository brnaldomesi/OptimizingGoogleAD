<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePotentialGainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potential_gains', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->string('date_range')->nullable();
            $table->integer('winners')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('cpa', 11, 2)->nullable();

            $table->decimal('cost_change', 11, 2)->nullable();

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
        Schema::table('potential_gains', function (Blueprint $table) {
            $table->dropIfExists('potential_gains');
        });
    }
}
