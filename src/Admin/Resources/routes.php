<?php

/** admin route that don't need session checkpoint */
Route::get(
    '/admin/login',
    'Xsanisty\Admin\Controller\AdminController:login',
    [
        'as' => 'admin.login',
        'before' => 'admin.guest'
    ]
);

Route::post('/admin/login', 'Xsanisty\Admin\Controller\AdminController:authenticate', ['as' => 'admin.authenticate']);
Route::get('/admin/logout', 'Xsanisty\Admin\Controller\AdminController:logout', ['as' => 'admin.logout']);

/** protected admin section */
Route::group(
    '/admin',
    function () {
        Route::get('/', 'Xsanisty\Admin\Controller\AdminController:index', ['as' => 'admin.home']);
    },
    ['before' => 'admin.auth']
);
