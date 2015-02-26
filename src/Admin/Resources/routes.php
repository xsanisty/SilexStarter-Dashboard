<?php

/** admin route that don't need session checkpoint */
Route::get('/admin/login', 'Xsanisty\Admin\Controller\AdminController:login')
     ->bind('admin.login')
     ->before(App::filter('admin.guest'));

Route::post('/admin/login', 'Xsanisty\Admin\Controller\AdminController:authenticate')
     ->bind('admin.authenticate');

Route::get('/admin/logout', 'Xsanisty\Admin\Controller\AdminController:logout')
     ->bind('admin.logout');

/** protected admin section */
Route::get('/admin', 'Xsanisty\Admin\Controller\AdminController:index')
     ->bind('admin.home')
     ->before(App::filter('admin.auth'));

Route::group('/admin', function(){
    Route::get('/', 'Xsanisty\Admin\Controller\AdminController:index');
})->before(App::filter('admin.auth'));
