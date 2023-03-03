<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id');
            $table->timestamps();
            $table->char('user_id', 36);
            $table->string('name')->nullable();
            $table->string('google_id')->nullable();
            $table->string('currency_code')->nullable();
            $table->dateTime('ad_performance_report_processed_at')->nullable();
            $table->primary('id');
            $table->index('user_id');

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
        Schema::dropIfExists('accounts');
    }
}
