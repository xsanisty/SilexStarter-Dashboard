<?php

/** put your module middlewares here */

/** redirect to login path when session is invalid (or return ajax response on ajax request) */
App::filter(
    'admin.auth',
    function () {
        if (!Sentry::check()) {
            if (Request::ajax()) {
                return Response::ajax(
                    'Invalid session!',
                    401,
                    false,
                    [
                        'code'      => 401,
                        'message'   => 'Unauthorized Access'
                    ]
                );
            } else {
                $intended = Url::to(Request::getRequestUri('request'));

                Session::flash('intended', $intended);
                Response::redirect(Url::to('admin.login'));
            }
        }
    }
);

App::filter(
    'admin.guest',
    function () {
        if (Sentry::check()) {
            return Response::redirect(Url::to('admin.home'));
        }
    }
);
