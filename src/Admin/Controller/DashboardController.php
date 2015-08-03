<?php

namespace Xsanisty\Admin\Controller;

use Xsanisty\Admin\DashboardModule;
use SilexStarter\Controller\DispatcherAwareController;

class DashboardController extends DispatcherAwareController
{
    public function __construct()
    {
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
    }
}
