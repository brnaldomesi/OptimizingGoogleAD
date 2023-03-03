<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recents', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('user_id', 36);
            $table->char('type', 100)->nullable();
            $table->text('anchor_text')->nullable();
            $table->text('url')->nullable();
            $table->char('account_id', 36);

            $table->primary('id');
            $table->index('user_id');

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
        Schema::table('recents', function (Blueprint $table) {
            Schema::dropIfExists('recents');
        });
    }
}
