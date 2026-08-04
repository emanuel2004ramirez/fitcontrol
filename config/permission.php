<?php

use App\Models\Permiso;
use App\Models\Rol;

return [
    'models' => [
        'permission' => Permiso::class,
        'role' => Rol::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permisos',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'role_user',
        'role_has_permissions' => 'permiso_rol',
    ],

    'column_names' => [
        'role_pivot_key' => 'role_id',
        'permission_pivot_key' => 'permiso_id',
        'model_morph_key' => 'user_id',
        'team_foreign_key' => 'team_id',
    ],

    'register_permission_check_method' => true,
    'register_octane_reset_listener' => false,
    'events_enabled' => false,
    'teams' => false,
    'team_resolver' => Spatie\Permission\DefaultTeamResolver::class,
    'use_passport_client_credentials' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];
