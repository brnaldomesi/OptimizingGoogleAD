<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDomainColumnToAdverts extends Migration
{
    public function up()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->string('domain', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropColumn('domain');
        });
    }
}
