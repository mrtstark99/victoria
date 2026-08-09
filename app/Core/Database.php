<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(string $path): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $directory = dirname($path);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new RuntimeException('Thư mục cơ sở dữ liệu không thể ghi.');
        }

        self::$connection = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        self::$connection->exec('PRAGMA foreign_keys = ON');
        self::$connection->exec('PRAGMA journal_mode = WAL');
        self::$connection->exec('PRAGMA busy_timeout = 5000');
        self::migrate(self::$connection);

        return self::$connection;
    }

    private static function migrate(PDO $database): void
    {
        $schema = dirname(__DIR__, 2) . '/database/schema.sql';
        $sql = file_get_contents($schema);
        if ($sql === false) {
            throw new RuntimeException('Không đọc được lược đồ cơ sở dữ liệu.');
        }
        $database->exec($sql);
    }
}
