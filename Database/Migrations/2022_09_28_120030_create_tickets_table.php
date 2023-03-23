<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('origin')->nullable()->default('internal');
            $table->string('subject');
            $table->longText('content');
            $table->longText('html')->nullable();
            $table->string('status')->default('open');
            $table->unsignedInteger('ticket_category_id')->nullable();

            $table->string('model_type')->nullable();
            $table->uuid('model_id')->nullable();

            $table->uuid('user_id');

            if(config('ticket.company_fk')) {
                $table->uuid(config('ticket.company_fk'))->nullable();
            }
            if(config('ticket.company_subject_fk')) {
                $table->uuid(config('ticket.company_subject_fk'))->nullable();
            }
            $table->uuid('agent_id')->nullable();

            $table->unsignedBigInteger('closing_category')->nullable();
            $table->text('closing_note')->nullable();
            $table->tinyInteger('closing_criticality')->default(0);

            $table->dateTime('last_opened_at')->nullable();
            $table->dateTime('last_closed_at')->nullable();
            $table->tinyInteger('last_comment_is_operator')->default(0);
            $table->dateTime('last_commented_at');
            $table->dateTime('sla_expiring');
            $table->integer('sla_blocked_minute')->default(0);
            $table->dateTime('sla_charge_expiring');
            $table->integer('sla_charge_processing')->default(0);
            $table->integer('sla_processing')->default(0);
            $table->string('screenshot1')->nullable();
            $table->string('screenshot2')->nullable();
            $table->string('screenshot3')->nullable();

            $table->timestamps();
        });


        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('CASCADE');

            if(config('ticket.company_fk')) {
                $table->foreign(config('ticket.company_fk'))->references('id')->on(config('ticket.company_tablename'))->onDelete('CASCADE');
            }

            if(config('ticket.company_subject_fk')) {
                $table->foreign(config('ticket.company_subject_fk'))->references('id')->on(config('ticket.company_subject_tablename'))->onDelete('CASCADE');
            }

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
        Schema::dropIfExists('tickets');
    }
}
