<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScormTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // scorm_model
        Schema::create('scorm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('resource');
            $table->string('version')->nullable();
            $table->string('hash_name')->nullable();
            $table->string('origin_file')->nullable();
            $table->string('origin_file_mime')->nullable();
            $table->double('ratio')->nullable();
            $table->string('uuid')->nullable();
            $table->timestamps();
        });

        // scorm_sco_model
        Schema::create('scorm_sco', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('scorm_id')->unsigned()->nullable();
            $table->string('uuid')->nullable();
            $table->bigInteger('sco_parent_id')->unsigned()->nullable();
            $table->string('entry_url')->nullable();
            $table->string('identifier')->nullable();
            $table->string('title')->nullable();
            $table->tinyInteger('visible')->nullable();
            $table->longText('sco_parameters')->nullable();
            $table->longText('launch_data')->nullable();
            $table->string('max_time_allowed')->nullable();
            $table->string('time_limit_action')->nullable();
            $table->tinyInteger('block')->nullable();
            $table->integer('score_int')->nullable();
            $table->decimal('score_decimal', 10, 7)->nullable();
            $table->decimal('completion_threshold', 10, 7)->nullable();
            $table->string('prerequisites')->nullable();
            $table->timestamps();

            $table->foreign('scorm_id')->references('id')->on('scorm');
        });

        // scorm_sco_tracking_model
        Schema::create('scorm_sco_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('sco_id')->unsigned();
            $table->string('uuid');
            $table->double('progression');
            $table->integer('score_raw')->nullable();
            $table->integer('score_min')->nullable();
            $table->integer('score_max')->nullable();
            $table->decimal('score_scaled', 10, 7)->nullable();
            $table->string('lesson_status')->nullable();
            $table->string('completion_status')->nullable();
            $table->integer('session_time')->nullable();
            $table->integer('total_time_int')->nullable();
            $table->string('total_time_string')->nullable();
            $table->string('entry')->nullable();
            $table->longText('suspend_data')->nullable();
            $table->string('credit')->nullable();
            $table->string('exit_mode')->nullable();
            $table->string('lesson_location')->nullable();
            $table->string('lesson_mode')->nullable();
            $table->tinyInteger('is_locked')->nullable();
            $table->longText('details')->comment('json_array')->nullable();
            $table->dateTime('latest_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('sco_id')->references('id')->on('scorm_sco');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('scorm_sco_tracking');
        Schema::drop('scorm_sco');
        Schema::drop('scorm');
    }
}
