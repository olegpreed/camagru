<?php

namespace Core;

class RateLimiter
{
    private static function getStoreDir(): string
    {
        $dir = sys_get_temp_dir() . '/camagru_rate_limit';
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        return $dir;
    }

    private static function getFilePath(string $key): string
    {
        return self::getStoreDir() . '/' . hash('sha256', $key) . '.json';
    }

    public static function hit(string $key, int $limit, int $windowSeconds): array
    {
        $now = time();
        $allowed = true;
        $data = [
            'count' => 0,
            'reset' => $now + $windowSeconds,
        ];

        $path = self::getFilePath($key);
        $fp = @fopen($path, 'c+');
        if ($fp !== false) {
            if (flock($fp, LOCK_EX)) {
                $stat = fstat($fp);
                if ($stat && $stat['size'] > 0) {
                    rewind($fp);
                    $contents = fread($fp, $stat['size']);
                    $decoded = json_decode($contents, true);
                    if (is_array($decoded) && isset($decoded['count'], $decoded['reset'])) {
                        $data = $decoded;
                    }
                }

                if ($data['reset'] <= $now) {
                    $data['count'] = 0;
                    $data['reset'] = $now + $windowSeconds;
                }

                if ($data['count'] < $limit) {
                    $data['count']++;
                    $allowed = true;
                } else {
                    $allowed = false;
                }

                rewind($fp);
                ftruncate($fp, 0);
                fwrite($fp, json_encode($data));
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

        $retryAfter = max(0, (int)($data['reset'] - $now));
        $remaining = max(0, $limit - $data['count']);

        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'reset' => (int)$data['reset'],
            'retry_after' => $retryAfter,
        ];
    }

    public static function clear(string $key): void
    {
        $path = self::getFilePath($key);
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
