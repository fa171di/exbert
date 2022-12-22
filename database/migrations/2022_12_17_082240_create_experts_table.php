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
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('favor_id')->nullable();
            $table->unsignedBigInteger('specialization_id');
            $table->string('title');
            $table->string('fees');
            $table->string('degree');
            $table->string('experience');
            $table->integer('slot_time')->nullable();
            $table->integer('evaluation')->default(0);
            $table->tinyInteger('is_deleted')->default(0)->comment('0=>active,1=>inactive');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('favor_id')->references('id')->on('favorites');
            $table->foreign('specialization_id')->references('id')->on('specializations');
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
        Schema::dropIfExists('experts');
    }
};
