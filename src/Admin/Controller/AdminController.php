<?php

namespace Xsanisty\Admin\Controller;

class AdminController
{

    protected $request;

    public function index()
    {
        return View::make('@xsanisty-dashboard/'.Config::get('@xsanisty-dashboard.config.template').'/index');
    }

    /**
     * Display login form
     * @return Response
     */
    public function login()
    {
        return View::make(
            '@xsanisty-dashboard/'.Config::get('@xsanisty-dashboard.config.template').'/login',
            [
                'message'   => Session::getFlash('message'),
                'email'     => Session::getFlash('email'),
                'remember'  => Session::getFlash('remember'),
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
        $redirect = Request::get('redirect');

        try {
            $credential = array(
                'email'     => $email,
                'password'  => Request::get('password')
            );

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if ($remember) {
                Sentry::loginAndRemember($user);
            } else {
                Sentry::login($user, false);
            }

            return Response::redirect($redirect ? Url::path($redirect) : Url::to('admin.home'));

        } catch (\Exception $e) {
            Session::flash('message', 'Invalid login!');
            Session::flash('email', $email);
            Session::flash('redirect', $redirect);
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
    public function checkLoggedIn()
    {

    }

    /**
     * Check if current session has no logged in user
     * @return Response
     */
    public function checkGuest()
    {

    }
}
