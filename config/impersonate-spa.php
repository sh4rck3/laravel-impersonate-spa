<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully-qualified class name of the user model.
    |
    */
    'user_model' => 'App\Models\User',

    /*
    |--------------------------------------------------------------------------
    | Session Keys
    |--------------------------------------------------------------------------
    |
    | The session keys used to store impersonation data.
    |
    */
    'session_key' => 'impersonate',
    'original_user_key' => 'original_user_id',

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the impersonation routes.
    |
    */
    'route_prefix' => 'impersonate',
    'middleware' => ['auth:sanctum', 'verified'],

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    |
    | Where to redirect after starting/stopping impersonation.
    |
    */
    'redirect_after_start' => 'dashboard',
    'redirect_after_stop' => 'dashboard',

    /*
    |--------------------------------------------------------------------------
    | Flash Messages
    |--------------------------------------------------------------------------
    |
    | Custom messages for impersonation actions.
    |
    */
    'messages' => [
        'start_success' => 'Personificação iniciada com sucesso para :name!',
        'stop_success' => 'Personificação finalizada com sucesso!',
        'unauthorized' => 'Você não tem permissão para personificar usuários.',
        'user_not_found' => 'Usuário não encontrado.',
        'cannot_impersonate_self' => 'Você não pode personificar a si mesmo.',
        'cannot_be_impersonated' => 'Este usuário não pode ser personificado.',
        'already_impersonating' => 'Você já está personificando um usuário.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inertia Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Inertia.js integration.
    |
    */
    'inertia' => [
        'share_impersonation_data' => true,
        'flash_message_key' => 'flash',
    ],
];