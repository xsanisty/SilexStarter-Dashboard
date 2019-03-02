<?php

namespace Xsanisty\Admin\Controller;

use Xsanisty\Admin\DashboardModule;

class AdminController
{
    protected $request;

    public function index()
    {
        return View::make('@silexstarter-dashboard/'.Config::get('@silexstarter-dashboard.config.template').'/dashboard');
    }

    /**
     * Display login form
     * @return Response
     */
    public function login()
    {
        $loginAttr = Config::get('sentry.users.login_attribute');

        return View::make(
            '@silexstarter-dashboard/'.Config::get('@silexstarter-dashboard.config.template').'/login',
            [
                'message'       => Session::flash('message'),
                'credential'    => [$loginAttr => Session::flash($loginAttr)],
                'remember'      => Session::flash('remember'),
                'intended'      => Session::flash('intended'),
                'login_attr'    => $loginAttr
            ]
        );
    }

    /**
     * Authenticate given credential against stored credential
     * @return Response
     */
    public function authenticate()
    {
        $remember   = Request::get('remember', false);
        $loginAttr  = Config::get('sentry.users.login_attribute');
        $login      = Request::get($loginAttr);
        $intended   = Request::get('intended');

        try {
            $credential = [
                $loginAttr  => $login,
                'password'  => Request::get('password')
            ];

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if ($remember) {
                Sentry::loginAndRemember($user);
            } else {
                Sentry::login($user, false);
            }

            if (isset($_COOKIE['invitation_token'])) {
                return Response::redirect(Url::to('byggplanen-backend.invitation.accept', ['token' => $_COOKIE['invitation_token']], true));
            }

            $defaultUrl = Url::to(Config::get('@silexstarter-dashboard.config.default_page'));
            return Response::redirect($intended ?  $intended : $defaultUrl);

        } catch (\Exception $e) {
            Session::flash('message', 'Invalid login!');
            Session::flash($loginAttr, $login);
            Session::flash('intended', $intended);
            Session::flash('remember', $remember);

            return Response::redirect(Url::to('admin.login'));
        }
    }

    /**
     * Loggout current logged in user
     * @return Response
     */
    public function logout()
    {
        Sentry::logout();

        return Response::redirect(Url::to('admin.login'));
    }

    /**
     * Check if current session has logged in user
     * @return Response     response with 401 status
     */
    public function loginCheckpoint()
    {
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

    /**
     * Check if current session has no logged in user
     * @return Response
     */
    public function guestCheckpoint()
    {
        if (Sentry::check()) {
            return Response::redirect(Url::to('admin.home'));
        }
    }
}
