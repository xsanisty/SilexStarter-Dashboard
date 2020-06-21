<?php

namespace Xsanisty\Admin\Event;

use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{

    public const LOGGED_IN  = 'user.logged_in';
    public const LOGGED_OUT = 'user.logged_out';

    protected $user;

    public function __construct($user)
    {

    }

    public function getUser()
    {
        return $this->user;
    }
}
