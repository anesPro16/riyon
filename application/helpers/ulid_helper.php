<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Generate ULID (Universally Unique Lexicographically Sortable Identifier)
 * Format: 26 karakter (time-based, sortable)
 * Ex: 01HFSWDP3R96E4G7R1S7J6V6QH
 */

if (!function_exists('generate_ulid')) {
    function generate_ulid()
    {
        // Epoch timestamp (milliseconds)
        $time = (int) (microtime(true) * 1000);

        // Encode timestamp (10 chars)
        $timeChars = encodeTime($time, 10);

        // 16 random chars
        $randomChars = '';
        for ($i = 0; $i < 16; $i++) {
            $randomChars .= encodeChar(random_int(0, 31));
        }

        return strtoupper($timeChars . $randomChars);
    }
}

if (!function_exists('encodeTime')) {
    function encodeTime($time, $length)
    {
        $encoding = '0123456789ABCDEFGHJKMNPQRSTVWXYZ'; // Crockford Base32
        $encoded = '';

        while ($time > 0 && strlen($encoded) < $length) {
            $mod = $time % 32;
            $encoded = $encoding[$mod] . $encoded;
            $time = (int) ($time / 32);
        }

        return str_pad($encoded, $length, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('encodeChar')) {
    function encodeChar($num)
    {
        $encoding = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        return $encoding[$num & 31];
    }
}
