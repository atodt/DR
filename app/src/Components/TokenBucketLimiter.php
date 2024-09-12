<?php

declare(strict_types=1);

namespace App\Components;

class TokenBucketLimiter
{
    private \Memcached $memcached;
    private string $key;
    private int $capacity;
    private float $tokensPerSecond;

    public function __construct(\Memcached $memcached, string $key, int $capacity, float $tokensPerSecond)
    {
        $this->memcached = $memcached;
        $this->key = $key;
        $this->capacity = $capacity;
        $this->tokensPerSecond = $tokensPerSecond;
    }

    public function allowRequest(): bool
    {
        $currentTime = microtime(true);
        $bucket = $this->memcached->get($this->key);

        if ($bucket === false) {
            $bucket = [
                'tokens' => $this->capacity,
                'last_refill' => $currentTime
            ];
        }

        $elapsedTime = $currentTime - $bucket['last_refill'];
        $bucket['tokens'] = min($this->capacity, $bucket['tokens'] + $elapsedTime * $this->tokensPerSecond);
        $bucket['last_refill'] = $currentTime;

        if ($bucket['tokens'] >= 1) {
            $bucket['tokens'] -= 1;
            $this->memcached->set($this->key, $bucket);
            return true;
        }

        $this->memcached->set($this->key, $bucket);
        return false;
    }
}
