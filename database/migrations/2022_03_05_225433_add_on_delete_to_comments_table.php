<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // photoが削除されたときにコメントも削除
        Schema::table('comments', function (Blueprint $table) {
          $table->foreign('photo_id')
                ->references('id')
                ->on('photos')
                ->onDelete('cascade')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
          $table->foreign('photo_id')
                ->references('id')
                ->on('photos')
                ->change();
        });
    }
}
