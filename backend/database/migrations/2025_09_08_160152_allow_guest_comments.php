<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE comments ALTER COLUMN user_id DROP NOT NULL');

        Schema::table('comments', function (Blueprint $table) {
            $table->string('guest_name', 100)->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_email']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE comments ALTER COLUMN user_id SET NOT NULL');
        }
    }
};
