<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultiAccountFieldsToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {

            $table->boolean('can_manage_clients')->nullable();
            $table->boolean('is_test_account')->nullable();
            $table->boolean('is_synced')->nullable();
            $table->boolean('is_active')->nullable();
            $table->string('parent_google_id')->nullable();

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

            $table->dropColumn('can_manage_clients');
            $table->dropColumn('is_test_account');
            $table->dropColumn('is_synced');
            $table->dropColumn('is_active');
            $table->dropColumn('parent_google_id');

        });
    }
}
