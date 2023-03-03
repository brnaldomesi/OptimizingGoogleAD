<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchJobIdToMutations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->uuid('batch_job_id')->nullable();
            $table->foreign('batch_job_id')->references('id')->on('batch_job')->onDelete('set null');
            $table->text('value', 500)->change();
            $table->text('response', 500)->change();
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
            $table->dropColumn('batch_job_id');
        });
    }
}
