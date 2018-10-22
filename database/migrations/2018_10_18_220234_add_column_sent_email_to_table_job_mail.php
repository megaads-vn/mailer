<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSentEmailToTableJobMail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_mail', function(Blueprint $table) {
           $table->enum('status', ['pending', 'sent', 'removed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_mail', function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
