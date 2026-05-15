<?php

declare(strict_types=1);

namespace App\Traits;

trait FormatsMetaTraderServerName
{
    /**
     * Normalizes a MetaTrader server name entered with spaces into the API-expected format.
     * e.g. "Deriv svg server 03" → "DerivSVG-Server-03"
     * Already-formatted values (no spaces) are returned unchanged.
     */
    private function formatServerName(string $server): string
    {
        $server = trim($server);

        if (! str_contains($server, ' ')) {
            return $server;
        }

        $parts = preg_split('/\s+/', $server);
        $result = [];
        $buffer = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (is_numeric($part)) {
                if ($buffer !== '') {
                    $result[] = $buffer;
                    $buffer = '';
                }
                $result[] = $part;
            } elseif (strlen($part) <= 3 && ctype_alpha($part)) {
                // Short alphabetical token (e.g. svg, bvi) — treat as uppercase abbreviation
                $buffer .= strtoupper($part);
            } elseif (strtolower($part) === 'server') {
                if ($buffer !== '') {
                    $result[] = $buffer;
                    $buffer = '';
                }
                $result[] = 'Server';
            } else {
                if ($buffer !== '') {
                    $result[] = $buffer;
                    $buffer = '';
                }
                $buffer = ucfirst(strtolower($part));
            }
        }

        if ($buffer !== '') {
            $result[] = $buffer;
        }

        return implode('-', $result);
    }
}
