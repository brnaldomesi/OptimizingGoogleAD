<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_subscribers', function (Blueprint $table) {

            $table->uuid('id')->nullable();
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_banned')->default(0)->nullable();
            $table->boolean('is_early_access')->default(1)->nullable();
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
        Schema::dropIfExists('email_subscribers');
    }
}
