<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameHeadlineColumnsInAdverts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->renameColumn('headline_part_1', 'headline_1');
            $table->renameColumn('headline_part_2', 'headline_2');
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
            $table->renameColumn('headline_1', 'headline_part_1');
            $table->renameColumn('headline_2', 'headline_part_2');
        });
    }
}
