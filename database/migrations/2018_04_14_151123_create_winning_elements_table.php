<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinningElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winning_elements', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('winnable_id', 36);
            $table->text('winnable_type');
            $table->char('type', 100)->nullable();
            $table->text('value')->nullable();
            $table->char('account_id', 36);

            $table->primary('id');
            $table->index('winnable_id');

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
        Schema::dropIfExists('winning_elements');
    }
}
