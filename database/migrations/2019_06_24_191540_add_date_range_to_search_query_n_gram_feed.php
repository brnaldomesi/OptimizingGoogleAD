<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateRangeToSearchQueryNGramFeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('search_query_n_gram_feed','date_range')) {
            Schema::table('search_query_n_gram_feed', function (Blueprint $table) {
                $table->string('date_range')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_query_n_gram_feed', function (Blueprint $table) {
            $table->dropColumn('date_range');
        });
    }
}
