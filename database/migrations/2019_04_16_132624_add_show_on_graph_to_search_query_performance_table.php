<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowOnGraphToSearchQueryPerformanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_query_performance', function (Blueprint $table) {
            $table->string('show_on_graph')->nullable();
            $table->string('graph_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_query_performance', function (Blueprint $table) {
            $table->dropColumn('show_on_graph');
            $table->dropColumn('graph_order');
        });
    }
}
