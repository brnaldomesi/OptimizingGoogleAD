<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToPythonQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('python_queue', function (Blueprint $table) {
            $table->dateTime('processing_started_at')->nullable();
            $table->renameColumn('processed_at', 'processing_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('python_queue', function (Blueprint $table) {
            $table->dropColumn('processing_started_at');
            $table->renameColumn('processing_completed_at', 'processed_at');
        });
    }
}
