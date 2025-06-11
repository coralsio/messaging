<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('messaging_discussions', function (Blueprint $table) {
    $table->string('thread_id')->after('subject')->nullable();
});
