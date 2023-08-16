<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->text('request_form_number');
            $table->text('gauge_type');
            $table->text('size');
            $table->text('serial_num');
            $table->dateTime('date_requested');
            $table->string('requested_by');
            $table->string('location');
            $table->dateTime('date_returned')->nullable();
            $table->string('returned_by')->nullable();
            $table->integer('status')->comment('1-requested,2-returned')->default(1);
            $table->integer('req_condition')->comment('1-ok,3-worn-out,4-missing')->default(1);
            $table->integer('incoming_id')->comment('this will be used to update the availability of gauge if this returned OKAY');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
