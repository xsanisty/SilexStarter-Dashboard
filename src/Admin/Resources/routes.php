<?php

/** admin route that don't need session checkpoint */
Route::get(
    Config::get('@silexstarter-dashboard.config.admin_prefix') . '/login',
    'Xsanisty\Admin\Controller\AdminController:login',
    [
        'as' => 'admin.login',
        'before' => 'admin.guest'
    ]
);

Route::post(
    Config::get('@silexstarter-dashboard.config.admin_prefix') . '/login',
    'Xsanisty\Admin\Controller\AdminController:authenticate',
    ['as' => 'admin.authenticate']
);

Route::get(
    Config::get('@silexstarter-dashboard.config.admin_prefix') . '/logout',
    'Xsanisty\Admin\Controller\AdminController:logout',
    ['as' => 'admin.logout']
);

/** protected admin section */
Route::group(
    Config::get('@silexstarter-dashboard.config.admin_prefix'),
    function () {
        Route::get('/', 'Xsanisty\Admin\Controller\AdminController:index', ['as' => 'admin.home']);
    },
    ['before' => 'admin.auth']
);
