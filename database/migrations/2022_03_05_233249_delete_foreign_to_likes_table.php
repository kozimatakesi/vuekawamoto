<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteForeignToLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // likesテーブルのforeign key photo_idを削除
        Schema::table('likes', function (Blueprint $table) {
          $table->dropForeign('likes_photo_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
          $table->foreign('photo_id')
                ->references('id')
                ->on('photos');
        });
    }
}
