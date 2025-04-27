<?php

namespace App\Service;

use Pusher\Pusher;

class PusherService
{
    private Pusher $pusher;

    public function __construct(
        string $appId,
        string $key,
        string $secret,
        string $cluster,
        string $host,
        int $port,
        string $scheme
    ) {
        $this->pusher = new Pusher(
            $key,
            $secret,
            $appId,
            [
                'cluster' => $cluster,
                'host' => str_replace('{cluster}', $cluster, $host),
                'port' => $port,
                'scheme' => $scheme,
                'useTLS' => $scheme === 'https'
            ]
        );
    }

    public function trigger(string $channel, string $event, array $data): void
    {
        $this->pusher->trigger($channel, $event, $data);
    }
}