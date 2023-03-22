<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->char('ticket_id', 36)->index('ticket_comments_ticket_id_foreign');
            $table->string('origin')->nullable()->default('desk');
            $table->longText('content');
            $table->longText('html')->nullable();
            $table->uuid('user_id');
            //$table->uuid('company_id');
            $table->string('screenshot1')->nullable();
            $table->string('screenshot2')->nullable();
            $table->string('screenshot3')->nullable();

            $table->timestamps();
        });

        Schema::table('ticket_comments', function (Blueprint $table) {
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('CASCADE');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_comments');
    }
}
