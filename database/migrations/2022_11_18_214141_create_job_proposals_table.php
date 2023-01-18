<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('type');
            $table->float('price');
            $table->integer('period');
            $table->text('description');
            $table->string('status');
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('rate')->nullable();
            $table->string('rate_message')->nullable();
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
        Schema::dropIfExists('job_proposals');
    }
};
