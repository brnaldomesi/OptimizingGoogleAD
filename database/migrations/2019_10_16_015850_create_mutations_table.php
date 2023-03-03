<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutations', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->char('type');
            $table->char('entity_google_id')->nullable();
            $table->char('entity_id')->nullable();
            $table->char('action');
            $table->char('attribute');
            $table->string('value');
            $table->char('destination_type');
            $table->char('destination_google_id');
            $table->char('response');
            $table->dateTime('executed_at')->nullable();
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
        Schema::dropIfExists('mutations');
    }
}
