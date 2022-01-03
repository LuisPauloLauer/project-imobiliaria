<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelEstateagenciesUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Rel_Estateagencies_Users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estateagency');
            $table->unsignedBigInteger('user');
            $table->timestamps();
            $table->foreign('estateagency')->references('id')->on('Estateagencies');
            $table->foreign('user')->references('id')->on('Users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Rel_Estateagencies_Users');
    }
}
