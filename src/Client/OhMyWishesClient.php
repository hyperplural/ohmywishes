<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Client;

use Hyperplural\Ohmywishes\Auth\TokenProviderInterface;
use Hyperplural\Ohmywishes\Exception\ApiException;
use Hyperplural\Ohmywishes\Http\NativeTransport;
use Hyperplural\Ohmywishes\Http\TransportInterface;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use Hyperplural\Ohmywishes\Raw\RawFacade;

final class OhMyWishesClient
{
    private readonly TransportInterface $transport;

    public function __construct(
        private readonly ClientConfig $config = new ClientConfig(),
        private readonly ?TokenProviderInterface $tokenProvider = null,
        ?TransportInterface $transport = null,
    ) {
        $this->transport = $transport ?? new NativeTransport($this->config->baseUri);
    }

    public function client(): \Hyperplural\Ohmywishes\Service\ClientService
    {
        return new \Hyperplural\Ohmywishes\Service\ClientService($this);
    }

    public function auth(?callable $captchaTokenResolver = null): \Hyperplural\Ohmywishes\Service\AuthService
    {
        return new \Hyperplural\Ohmywishes\Service\AuthService($this, $captchaTokenResolver);
    }

    public function users(): \Hyperplural\Ohmywishes\Service\UserService
    {
        return new \Hyperplural\Ohmywishes\Service\UserService($this);
    }

    public function wishes(): \Hyperplural\Ohmywishes\Service\WishService
    {
        return new \Hyperplural\Ohmywishes\Service\WishService($this);
    }

    public function wishLists(): \Hyperplural\Ohmywishes\Service\WishListService
    {
        return new \Hyperplural\Ohmywishes\Service\WishListService($this);
    }

    public function settings(): \Hyperplural\Ohmywishes\Service\SettingsService
    {
        return new \Hyperplural\Ohmywishes\Service\SettingsService($this);
    }

    public function reference(): \Hyperplural\Ohmywishes\Service\ReferenceService
    {
        return new \Hyperplural\Ohmywishes\Service\ReferenceService($this);
    }

    public function raw(): RawFacade
    {
        return new RawFacade($this);
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @param array<string, mixed>|null $multipart
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?array $multipart = null,
    ): TransportResponse {
        $response = $this->transport->request(
            $method,
            $path,
            $query,
            $body,
            array_merge($this->defaultHeaders(), $headers),
            $multipart,
        );

        if (!$response->isSuccessful()) {
            throw ApiException::httpError($response->statusCode, $response->body);
        }

        return $response;
    }

    public function transport(): TransportInterface
    {
        return $this->transport;
    }

    public function config(): ClientConfig
    {
        return $this->config;
    }

    public function token(): ?string
    {
        return $this->tokenProvider?->getToken();
    }

    /**
     * @return array<string, string>
     */
    private function defaultHeaders(): array
    {
        $headers = [
            'accept' => '*/*',
            'content-type' => 'application/json',
            'accept-language' => $this->config->locale,
            'pragma' => 'no-cache',
            'cache-control' => 'no-cache',
            'x-content-region' => $this->config->contentRegion,
        ];

        if ($this->config->userAgent !== null) {
            $headers['user-agent'] = $this->config->userAgent;
        }

        if ($this->tokenProvider?->getToken() !== null) {
            $headers['x-access-token'] = $this->tokenProvider->getToken();
        }

        return $headers;
    }
}
