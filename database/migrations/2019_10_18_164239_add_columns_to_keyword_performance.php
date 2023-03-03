<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToKeywordPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keyword_performance', function (Blueprint $table) {
            $table->char('search_impression_share', 10)->nullable();
            $table->decimal('bounce_rate', 11, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keyword_performance', function (Blueprint $table) {
            $table->dropColumn('search_impression_share');
            $table->dropColumn('bounce_rate');
        });
    }
}
