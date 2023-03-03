<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityColumntsToRecents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recents', function (Blueprint $table) {
            $table->char('entity_id', 36)->nullable();
            $table->string('entity_type')->nullable();

            $table->dropColumn('type');
            $table->dropColumn('anchor_text');
            $table->dropColumn('url');
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
            $table->dropColumn('entity_id');
            $table->dropColumn('entity_type');

            $table->char('type', 100)->nullable();
            $table->text('anchor_text')->nullable();
            $table->text('url')->nullable();
        });
    }
}
