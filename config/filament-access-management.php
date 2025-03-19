<?php

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\PermissionResource;
use SolutionForest\FilamentAccessManagement\Pages;
use SolutionForest\FilamentAccessManagement\Models;
use SolutionForest\FilamentAccessManagement\Resources;
use SolutionForest\FilamentAccessManagement\Http\Middleware;

return [
    /**
     * Except from authentication
     */
    'auth' => [
        'except' => [
            '/',
            '/login',
            '/error*',
        ],
    ],
    'filament' => [
        'path_permission_checking' => [
            /**
             * Determine the permissions if the `Action` have `url`
             */
            'action' => true,
        ],
        'middleware' => [
            'base' => [
                Middleware\Authenticate::class,
            ],
        ],
        'navigation' => [
            /**
             * Using db based filament navigation if true.
             */
            'enabled' => false,
            /**
             * Table name db based filament navigation.
             */
            'table_name' => 'filament_menu',
            /**
             * Filament Menu Model.
             */
            'model' => Models\Menu::class,
        ],
        'navigationIcon' => [
            /**
             * Default ICON of the page.
             */
            'default' => 'heroicon-o-document-text',
            'user' => 'heroicon-o-user',
            'role' => 'heroicon-o-user-group',
            'permission' => 'heroicon-o-lock-closed',
            'menu' => 'heroicon-o-bars-3-bottom-left',
        ],
        'pages' => [
            Pages\Menu::class,
        ],
        'resources' => [
            UserResource::class,
            RoleResource::class,
            PermissionResource::class,
        ]
    ],
    'roles' => [
        'super-admin' => [
            'name' => 'super-admin',
            'role_permissions' => [
                'users.*',
                'roles.*',
                'permissions.*',
                'menu.*',
            ],
        ],
    ],

    /**
     *
     * Default permissions to install
     *
     */

    'permissions' => [
        'users.*'           => '/assets/users*',
        'users.viewAny'     => '/assets/users',
        'users.view'        => '/assets/users/*',
        'users.create'      => '/assets/users/create',
        'users.update'      => '/assets/users/edit/*',
        'users.delete'      => '/assets/users/*',
        'users.deleteAny'   => '/assets/users/*',

        'roles.*'           => '/assets/roles*',
        'roles.viewAny'     => '/assets/roles',
        'roles.view'        => '/assets/roles/*',
        'roles.create'      => '/assets/roles/create',
        'roles.update'      => '/assets/roles/edit/*',
        'roles.delete'      => '/assets/roles/*',
        'roles.deleteAny'   => '/assets/roles/*',

        'permissions.*'         => '/assets/permissions*',
        'permissions.viewAny'   => '/assets/permissions',
        'permissions.view'      => '/assets/permissions/*',
        'permissions.create'    => '/assets/permissions/create',
        'permissions.update'    => '/assets/permissions/edit/*',
        'permissions.delete'    => '/assets/permissions/*',
        'permissions.deleteAny' => '/assets/permissions/*',

        'menu.*'                => '/assets/menu*',
        'menu.viewAny'          => '/assets/menu',
        'menu.view'             => '/assets/menu/*',
        'menu.create'           => '/assets/menu/create',
        'menu.update'           => '/assets/menu/edit/*',
    ],

    /**
     *
     * Cache settings
     *
     */
    'cache' => [
        /**
         *
         * User's permission cache settings
         *
         */
        'user_permissions' => [
            /*
            * By default all permissions are cached for 24 hours to speed up performance.
            * When permissions or roles are updated the cache is flushed automatically.
            */

            'expiration_time' => \DateInterval::createFromDateString('24 hours'),

            /*
            * The cache key used to store all permissions.
            */

            'key_prefix' => 'user_spatie.permission.cache',

            /*
            * You may optionally indicate a specific cache driver to use for permission and
            * role caching using any of the `store` drivers listed in the cache.php config
            * file. Using 'default' here means to use the `default` set in cache.php.
            */

            'store' => 'default',

            'tag' => 'user_permissions',
        ],

        /**
         *
         * Filament navigation cache settings
         *
         */
        'navigation' => [
            'expiration_time' => \DateInterval::createFromDateString('24 hours'),
            'key' => 'filament_navigation',
        ]
    ],
];
