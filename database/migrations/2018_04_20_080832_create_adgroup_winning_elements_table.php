<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdgroupWinningElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adgroup_winning_elements', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('adgroup_id', 36);
            $table->char('type', 100)->nullable();
            $table->text('value')->nullable();
            $table->char('account_id', 36);

            $table->primary('id');
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
        Schema::table('adgroup_winning_elements', function (Blueprint $table) {
            Schema::dropIfExists('adgroup_winning_elements');
        });
    }
}
