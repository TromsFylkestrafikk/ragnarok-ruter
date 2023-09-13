<?php

namespace Ragnarok\Ruter\Services;

use Illuminate\Support\Facades\Http;
use Ragnarok\Sink\Traits\LogPrintf;

/**
 * Handle authentication for Ruter services
 */
class RuterAuthToken
{
    use LogPrintf;

    /**
     * Auth token received from Ruter.
     *
     * @var string|null
     */
    protected $apiToken = null;

    /**
     * Timer used to track age of api token.
     *
     * @var int
     */
    protected $tokenExpires = 0;

    public function __construct(protected array $config)
    {
        $this->logPrintfInit("[Ruter Token]: ");
    }

    public function getApiToken()
    {
        $this->requestToken();
        return $this->apiToken;
    }

    protected function requestToken()
    {
        if ($this->apiToken && time() < $this->tokenExpires) {
            $this->debug('Requesting API token: Valid. Nothing to do.');
            return;
        }
        $this->debug('Requesting API token ...');
        $encodeB64 = base64_encode(sprintf('%s:%s', $this->config['client']['id'], $this->config['client']['secret']));
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $encodeB64,
            'Cache-Control' => 'no-cache'
        ])->asForm()->post($this->config['token_endpoint'], [
            'grant_type' => 'client_credentials',
            'scope' => $this->config['scope']
        ]);

        $result = $response->json();
        $this->apiToken = $result['access_token'];
        $tokenLifetime = $result['expires_in'];
        $this->tokenExpires = time() + intval($tokenLifetime);
        $this->debug('Token received. Expires in %d minutes', $tokenLifetime / 60);
    }
}
