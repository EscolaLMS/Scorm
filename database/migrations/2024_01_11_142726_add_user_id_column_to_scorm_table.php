<?php

use EscolaLms\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumnToScormTable extends Migration
{
    public function up(): void
    {
        Schema::table('scorm', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scorm', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
