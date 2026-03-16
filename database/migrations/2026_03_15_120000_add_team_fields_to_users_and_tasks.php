<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('leader')->after('password');
            $table->foreignId('leader_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });

        DB::statement("UPDATE users SET role = 'leader' WHERE role IS NULL");
        DB::statement('UPDATE tasks SET assigned_to_user_id = user_id WHERE assigned_to_user_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('leader_id');
            $table->dropColumn('role');
        });
    }
};
