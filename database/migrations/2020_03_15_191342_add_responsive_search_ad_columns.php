<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResponsiveSearchAdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adverts', function (Blueprint $table) {
          $table->longText('responsive_search_ad_descriptions')->nullable();
          $table->longText('responsive_search_ad_headlines')->nullable();
          $table->string('responsive_search_ad_path_1')->nullable();
          $table->string('responsive_search_ad_path_2')->nullable();
          $table->string('short_headline')->nullable();
          $table->string('ad_strength_info')->nullable();
          $table->string('automated')->nullable();
          $table->string('combined_approval_status')->nullable();
          $table->string('creative_final_mobile_urls')->nullable();
          $table->string('creative_final_url_suffix')->nullable();
          $table->string('device_preference')->nullable();
          $table->string('policy_summary')->nullable();
          $table->string('google_adgroup_id')->nullable();
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
          $table->dropColumn('responsive_search_ad_descriptions');
          $table->dropColumn('responsive_search_ad_headlines');
          $table->dropColumn('responsive_search_ad_path_1');
          $table->dropColumn('responsive_search_ad_path_2');
          $table->dropColumn('short_headline');
          $table->dropColumn('ad_strength_info');
          $table->dropColumn('automated');
          $table->dropColumn('combined_approval_status');
          $table->dropColumn('creative_final_mobile_urls');
          $table->dropColumn('creative_final_url_suffix');
          $table->dropColumn('device_preference');
          $table->dropColumn('policy_summary');
          $table->dropColumn('google_adgroup_id');
        });
    }
}
