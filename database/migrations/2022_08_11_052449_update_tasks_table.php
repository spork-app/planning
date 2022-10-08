<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('status_id')->nullable()->change();
            $table->dateTime('completed_at')->nullable()->after('status_id');
            // This would be the task lists in the UI.
            $table->unsignedInteger('feature_list_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('status_id')->nullable(false)->change();
            $table->dropColumn('completed_at');
            $table->dropColumn('feature_list_id');
        });
    }
}
