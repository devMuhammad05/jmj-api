<?php

if (! function_exists('format_status_text')) {
    /**
     * Format status text by removing underscores and capitalizing words.
     *
     * @param string $text
     * @return string
     */
    function format_status_text(string $text): string
    {
        return ucwords(str_replace('_', ' ', $text));
    }
}
