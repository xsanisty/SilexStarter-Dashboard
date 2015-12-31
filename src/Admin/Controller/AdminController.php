<?php

namespace Xsanisty\Admin\Controller;

use SilexStarter\Controller\DispatcherAwareController;
use Xsanisty\Admin\DashboardModule;

class AdminController extends DispatcherAwareController
{

    protected $request;

    public function index()
    {
        $this->dispatcher->dispatch(DashboardModule::INIT);
        return View::make('@silexstarter-dashboard/'.Config::get('@silexstarter-dashboard.config.template').'/dashboard');
    }

    /**
     * Display login form
     * @return Response
     */
    public function login()
    {
        return View::make(
            '@silexstarter-dashboard/'.Config::get('@silexstarter-dashboard.config.template').'/login',
            [
                'message'   => Session::flash('message'),
                'email'     => Session::flash('email'),
                'remember'  => Session::flash('remember'),
                'intended'  => Session::flash('intended')
            ]
        );
    }

    /**
     * Authenticate given credential against stored credential
     * @return Response
     */
    public function authenticate()
    {
        $remember = Request::get('remember', false);
        $email    = Request::get('email');
        $intended = Request::get('intended');

        try {
            $credential = [
                'email'     => $email,
                'password'  => Request::get('password')
            ];

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if ($remember) {
                Sentry::loginAndRemember($user);
            } else {
                Sentry::login($user, false);
            }

            $defaultUrl = Url::to(Config::get('@silexstarter-dashboard.config.default_page'));

            return Response::redirect($intended ?  $intended : $defaultUrl);

        } catch (\Exception $e) {
            Session::flash('message', 'Invalid login!');
            Session::flash('email', $email);
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
