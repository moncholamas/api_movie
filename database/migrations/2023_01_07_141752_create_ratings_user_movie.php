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
        Schema::create('ratings_user_movie', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('rating')->range(1,5)->nullable(); // recibe valores de 1 a 5
            $table->text('commentary', 400)->nullable();
            $table->boolean('favorite')->default(false);
            // referencia al usuario
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')
                ->references('id')
                ->on('users'); // no agrego onDelete porque tiene borrado logico
           
            // referencia a la pelicula
            $table->unsignedBigInteger('id_movie');
            $table->foreign('id_movie')
                ->references('id')
                ->on('movies'); // no agrego onDelete porque tiene borrado logico
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings_user_movie');
    }
};
