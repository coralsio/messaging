<?php

use Illuminate\Support\Facades\Route;

Route::controller('MessageController')->prefix('messages')->group(function () {
    Route::get('fetch-more-messages/{message}', 'fetchMoreMessages');
    Route::post('broadcast/{message}', 'broadcastMessage');
});

Route::post('discussions/{discussion}/mark-as-read', 'DiscussionsController@markAsRead');
Route::get('discussions/un-read-messages', 'DiscussionsController@discussionsCountForUnReadMessages');

Route::post('discussions/{discussion}/delete-conversation', 'DiscussionsController@deleteConversation');

Route::apiResource('messages', 'MessageController')->only('store');
Route::apiResource('discussions', 'DiscussionsController')->only('index', 'show');
