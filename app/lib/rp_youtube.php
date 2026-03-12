<?php

/**
 * YoutubePub — minimal YouTube Data API v3 client.
 *
 * Wraps cURL requests to the YouTube API, automatically appending
 * the API key to every request.  SSL verification is always enabled.
 */
class YoutubePub
{
    private const BASE_URL = 'https://www.googleapis.com/youtube/v3/';

    private string $apiKey;

    /**
     * @param string $apiKey  YouTube Data API v3 key
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Calls a YouTube API endpoint and returns the decoded JSON response.
     *
     * @param  string  $endpoint  API endpoint name, e.g. "channels" or "playlistItems"
     * @param  array<string, string>  $params  Query parameters (key is added automatically)
     * @return array<string, mixed>
     *
     * @throws \RuntimeException  On cURL failure or non-200 HTTP status
     */
    public function get(string $endpoint, array $params = []): array
    {
        $params['key'] = $this->apiKey;
        $url = $this->buildUrl($endpoint, $params);
        $body = $this->fetch($url);

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Builds the full request URL for an endpoint.
     *
     * @param  string  $endpoint
     * @param  array<string, string>  $params
     */
    private function buildUrl(string $endpoint, array $params): string
    {
        return self::BASE_URL . $endpoint . '?' . http_build_query($params);
    }

    /**
     * Fetches a URL via cURL with SSL verification enabled.
     *
     * @param  string  $url
     * @return string  Raw response body
     *
     * @throws \RuntimeException  On cURL error or non-2xx HTTP response
     */
    private function fetch(string $url): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            throw new \RuntimeException("cURL error ($errno): $error");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("YouTube API returned HTTP $httpCode for $url");
        }

        return (string) $body;
    }
}
