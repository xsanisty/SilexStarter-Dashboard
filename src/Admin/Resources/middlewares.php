<?php

/** put your module middlewares here */

/** redirect to login path when session is invalid (or return ajax response on ajax request) */
App::filter(
    'admin.auth',
    function () {
        if (!Sentry::check()) {
            if (Request::ajax()) {
                return Response::ajax(
                    'Unauthorized Access',
                    401,
                    [
                        'code'      => 401,
                        'message'   => 'Unauthorized Access'
                    ]
                );
            } else {
                $intended = App::make('request')->getRequestUri();

                Session::flash('intended', $intended);

                return Response::redirect(Url::to('admin.login', [], true));
            }
        }
    }
);

/** redirect to admin page if trying to access login page on valid session */
App::filter(
    'admin.guest',
    function () {
        $default_url = Url::to(Config::get('@silexstarter-dashboard.config.default_page'));
        if (Sentry::check()) {
            return Response::redirect($default_url);
        }
    }
);
