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
            'sidebar_renderer'      => Xsanisty\Admin\Helper\SidebarMenuRenderer::class,
            'navbar_renderer'       => Xsanisty\Admin\Helper\NavbarMenuRenderer::class,
            'breadcrumb_renderer'   => Xsanisty\Admin\Helper\BreadcrumbMenuRenderer::class,
            'skin'                  => 'blue',
        ],
        'AdminLTE'  => [
            'sidebar_renderer'      => Xsanisty\Admin\Helper\LteSidebarMenuRenderer::class,
            'navbar_renderer'       => Xsanisty\Admin\Helper\LteNavbarMenuRenderer::class,
            'breadcrumb_renderer'   => Xsanisty\Admin\Helper\LteBreadcrumbMenuRenderer::class,
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
