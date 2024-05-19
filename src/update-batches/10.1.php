<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('messaging_messages', function (Blueprint $table) {
    $table->text('body')->nullable()->change();
    $table->string('status')->after('body')->default('active');
    $table->softDeletes();
});

Schema::table('messaging_discussions', function (Blueprint $table) {
    $table->softDeletes();
});

Schema::table('messaging_participations', function (Blueprint $table) {
    $table->integer('unread_counts')->after('last_read')->default(0);
    $table->softDeletes();
});
