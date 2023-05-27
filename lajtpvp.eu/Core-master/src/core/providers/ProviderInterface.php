<?php

declare(strict_types=1);

namespace core\providers;

use core\Main;

abstract class ProviderInterface {

    public function __construct(private Main $plugin) {
    }

    abstract public function getName(): string;

    abstract public function getDatabase();

    abstract public function executeQuery(string $query, array $params = []): void;

    abstract public function executeQueries(string $query, array $params = []): void;

    abstract public function getQueryResult(string $query, bool $fetchAll = false, array $params = []): ?array;

    abstract public function getQuery(string $query,  array $params = []): ?\PDOStatement;

    abstract public function close(): void;

    abstract public function checkConnection(): bool;

    public function getPlugin(): Main {
        return $this->plugin;
    }
}