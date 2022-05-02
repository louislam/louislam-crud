<?php

function polyfill() : void {
    /**
     * BC Polyfill for PHP 8
     *
     * Please remove these functions from your code
     */
    if (!function_exists('get_magic_quotes_gpc')) {
        function get_magic_quotes_gpc(): bool {
            return false;
        }
    }
}
