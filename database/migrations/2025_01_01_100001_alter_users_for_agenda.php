<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            // keep default columns (id, name, email, password, remember_token, timestamps)
            if (!Schema::hasColumn('users', 'username')) {
                $t->string('username', 50)->after('id')->unique();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $t->enum('role', ['admin','teacher','student'])->after('password')->index();
            }
            if (!Schema::hasColumn('users', 'display_name')) {
                $t->string('display_name', 100)->nullable()->after('username');
            }
            // make email optional (we log in with username)
            $t->string('email')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'display_name')) $t->dropColumn('display_name');
            if (Schema::hasColumn('users', 'role')) $t->dropColumn('role');
            if (Schema::hasColumn('users', 'username')) $t->dropUnique(['username']);
            if (Schema::hasColumn('users', 'username')) $t->dropColumn('username');
            // revert email to not nullable if needed (safe default)
            $t->string('email')->nullable(false)->change();
        });
    }
};
