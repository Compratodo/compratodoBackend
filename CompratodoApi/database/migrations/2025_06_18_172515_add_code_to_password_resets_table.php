<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('password_resets', function (Blueprint $table) {
        $table->string('code')->nullable()->after('token');
    });
}

public function down()
{
    Schema::table('password_resets', function (Blueprint $table) {
        $table->dropColumn('code');
    });
}

};
