<?php

if (!function_exists('is_impersonating')) {
    /**
     * Verifica se o usuário atual está personificando outro usuário.
     *
     * @return bool
     */
    function is_impersonating(): bool
    {
        return session()->has('impersonate');
    }
}

if (!function_exists('get_impersonated_user_id')) {
    /**
     * Obtém o ID do usuário que está sendo personificado.
     *
     * @return int|null
     */
    function get_impersonated_user_id(): ?int
    {
        return session('impersonate');
    }
}

if (!function_exists('get_original_user_id')) {
    /**
     * Obtém o ID do usuário original que iniciou a personificação.
     *
     * @return int|null
     */
    function get_original_user_id(): ?int
    {
        return session('original_user_id');
    }
}