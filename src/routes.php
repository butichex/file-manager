<?php

use Illuminate\Support\Facades\Route;

Route::namespace(config('file-manager.route.namespace', 'dyutin\\FileManager\\Controllers'))
  ->prefix(config('file-manager.route.prefix', ''))
    ->domain(config('file-manager.route.domain', ''))
    ->middleware(config('file-manager.route.middleware', []))
    ->group(static function () {
        Route::get('/', 'FileManagerController@index')
              ->name(config('file-manager.route.as', ''));
        Route::post('/terminal/command', 'TerminalController@run');
        Route::prefix('/files')->group(static function () {
            Route::post('/', 'FileManagerController@showBaseItems');
            Route::post('/children', 'FileManagerController@children');
            Route::post('/content', 'FileManagerController@getFileContent');
            Route::post('/save-content', 'FileManagerController@saveFileContent');
            Route::post('/create/file', 'FileManagerController@createFile');
            Route::post('/create/folder', 'FileManagerController@createFolder');
            Route::post('/rename', 'FileManagerController@rename');
            Route::post('/delete', 'FileManagerController@delete');
            Route::post('/upload', 'FileManagerController@upload');
            Route::post('/compress', 'FileManagerController@compress');
            Route::post('/unzip', 'FileManagerController@unzip');
            Route::post('/copy', 'FileManagerController@copy');
            Route::post('/cut', 'FileManagerController@cut');
        });
    });
