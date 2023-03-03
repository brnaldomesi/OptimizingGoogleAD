<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('adgroup_id', 36);
            $table->string('keyword_text');
            $table->string('keyword_match_type');
            $table->string('google_id');
            $table->string('status');
            $table->string('account_id');
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
        Schema::dropIfExists('keywords');
    }
}
