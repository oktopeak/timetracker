<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropForeign(['user_id']); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('vacations', function (Blueprint $table) {
            $table->dropForeign(['user_id']); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);  
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_foreign_keys', function (Blueprint $table) {
            Schema::table('check_ins', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users');
            });

            Schema::table('vacations', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users');
            });

            Schema::table('leaves', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users');
            });
        });
    }
};
