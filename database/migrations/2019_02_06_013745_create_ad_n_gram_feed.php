<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdNgramFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_n_gram_feed', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('account_id', 36);
            $table->char('n_gram_id', 36);
            $table->string('priority');
            $table->string('headline');
            $table->string('message');
            $table->string('suggestion');
            $table->date('display_from_date')->nullable();
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
        Schema::dropIfExists('ad_n_gram_feed');
    }
}
