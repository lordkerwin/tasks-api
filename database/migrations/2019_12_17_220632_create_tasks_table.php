<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->longText('body')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->bigInteger('assignee_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('tasks');
    }
}
