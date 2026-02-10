<?php

namespace App\Services;

class UrlValidator
{
    /**
     * Validate a URL for scanning purposes.
     *
     * @return string|true Returns true if valid, error message string if invalid
     */
    public function validateForScanning(string $url): string|true
    {
        if (! app()->environment('local')) {
            // Check for localhost
            if ($this->isLocalhost($url)) {
                return 'Cannot scan localhost or local URLs';
            }

            // Check for IP addresses
            if ($this->isIpAddress($url)) {
                return 'Cannot scan IP addresses';
            }
        }

        // Check protocol
        if (! $this->hasValidScheme($url)) {
            return 'URL must use HTTP or HTTPS protocol';
        }

        // Check for valid domain
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host || ! filter_var('http://'.$host, FILTER_VALIDATE_URL)) {
            return 'Invalid domain name';
        }

        return true;
    }

    /**
     * Check if URL is localhost or local domain.
     */
    public function isLocalhost(string $url): bool
    {
        return (bool) preg_match('/(localhost|127\.0\.0\.1|\.local|\.test)/i', $url);
    }

    /**
     * Check if URL is an IP address.
     */
    public function isIpAddress(string $url): bool
    {
        return (bool) preg_match('/^https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url);
    }

    /**
     * Check if URL has a valid HTTP/HTTPS scheme.
     */
    public function hasValidScheme(string $url): bool
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https']);
    }
}
