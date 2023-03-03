<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPerformanceColumnsFromAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('impressions');
            $table->dropColumn('clicks');
            $table->dropColumn('conversions');
            $table->dropColumn('average_position');
            $table->dropColumn('cost');
            $table->dropColumn('average_cpc');
            $table->dropColumn('conversion_value');
            $table->dropColumn('cpa');
            $table->dropColumn('roas');
            $table->dropColumn('conversion_rate');
            $table->dropColumn('ctr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('conversions')->nullable();
            $table->decimal('average_position', 11, 1)->nullable();
            $table->decimal('cost', 11, 2)->nullable();
            $table->decimal('average_cpc', 11, 2)->nullable();
            $table->decimal('conversion_value', 11, 2)->nullable();
            $table->decimal('cpa', 11, 2)->nullable();
            $table->decimal('roas', 11, 2)->nullable();
            $table->decimal('conversion_rate', 11, 2)->nullable();
            $table->decimal('ctr', 11, 2)->nullable();
        });
    }
}
