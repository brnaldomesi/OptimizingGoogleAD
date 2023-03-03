<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MutationsTableMakeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->string('destination_type')->nullable()->change();
            $table->string('destination_google_id')->nullable()->change();
            $table->longText('response')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->string('destination_type')->nullable(false)->change();
            $table->string('destination_google_id')->nullable(false)->change();
            $table->longText('response')->nullable(false)->change();
        });
    }
}
