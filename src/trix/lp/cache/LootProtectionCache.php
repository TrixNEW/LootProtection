<?php
declare(strict_types=1);

namespace trix\lp\cache;

final class LootProtectionCache {

    private static array $entries = [];

    public static function add(int $entityId, string $owner, float $expiry): void {
        self::$entries[$entityId] = [$owner, $expiry];
    }

    public static function has(int $entityId): bool {
        return isset(self::$entries[$entityId]);
    }

    public static function evaluate(int $entityId, string $player): bool {
        [$owner, $expiry] = self::$entries[$entityId];

        if (microtime(true) >= $expiry) {
            unset(self::$entries[$entityId]);
            return true;
        }

        if ($player === $owner) {
            unset(self::$entries[$entityId]);
            return true;
        }

        return false;
    }

    public static function getRemaining(int $entityId): int {
        return (int) max(1, ceil(self::$entries[$entityId][1] - microtime(true)));
    }

    public static function remove(int $entityId): void {
        unset(self::$entries[$entityId]);
    }

    public static function parse(string $data): ?array {
        $pos = strpos($data, '--$$$--');
        if ($pos === false) return null;

        $owner = substr($data, 0, $pos);
        $expiry = (float) substr($data, $pos + 7);

        if ($owner === '' || $expiry <= 0) return null;

        return [$owner, $expiry];
    }
}