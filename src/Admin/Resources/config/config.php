<?php

return [
    'template'      => 'AdminLTE',
    'use_logo'      => true,
    'logo'          => '@silexstarter-dashboard/img/xsanisty.png',
    'logo_alt'      => '<b>X</b>sanisty',
    'logo_mini'     => '@silexstarter-dashboard/img/xsanisty-x.png',
    'logo_mini_alt' => 'X',
    'logo_height'   => 35,
    'admin_prefix'  => '/admin',
    'default_page'  => 'admin.home',

    'templates'                 => [
        'RDash'     => [
            'sidebar_renderer'      => 'Xsanisty\Admin\Helper\SidebarMenuRenderer',
            'navbar_renderer'       => 'Xsanisty\Admin\Helper\NavbarMenuRenderer',
            'breadcrumb_renderer'   => 'Xsanisty\Admin\Helper\BreadcrumbMenuRenderer',
            'skin'                  => 'blue',
        ],
        'AdminLTE'  => [
            'sidebar_renderer'      => 'Xsanisty\Admin\Helper\LteSidebarMenuRenderer',
            'navbar_renderer'       => 'Xsanisty\Admin\Helper\LteNavbarMenuRenderer',
            'breadcrumb_renderer'   => 'Xsanisty\Admin\Helper\LteBreadcrumbMenuRenderer',
            'skin'                  => 'purple-light',
            'fixed_header'          => false
        ],
        'Gentelella' => [
            'sidebar_renderer'      => '',
            'navbar_renderer'       => '',
            'breadcrumb_renderer'   => '',
        ]
    ]
];
