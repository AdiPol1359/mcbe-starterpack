<?php

declare(strict_types=1);

namespace core\providers\data;

use core\Main;
use core\providers\ProviderInterface;
use PDO;

class SQLiteProvider extends ProviderInterface {

    private ?PDO $database;

    public function __construct(Main $plugin) {
        parent::__construct($plugin);

        $this->database = new PDO("sqlite:" . $plugin->getDataFolder() . "/data/database.sqlite");
    }

    public function getName() : string {
        return "SQLite";
    }

    public function getDatabase() : PDO {
        return $this->database;
    }

    public function executeQuery(string $query, array $params = []) : void {
        $result = $this->database->prepare($query, $params);

        if ($result === false) {
            $this->getPlugin()->getLogger()->error("Wystapil blad w QueryResult: ");
            return;
        }

        $this->getPlugin()->getLogger()->debug("Query executed: " . $query);

        $result->execute($params);
    }

    public function executeQueries(string $query, array $params = []) : void {
        $queries = explode(";", $query);

        foreach($queries as $query) {
            if($query === "") {
                continue;
            }

            $this->executeQuery($query);
        }
    }

    public function getQueryResult(string $query, bool $fetchAll = false, array $params = []) : ?array {
        $result = $this->database->prepare($query);

        if ($result === false) {
            $this->getPlugin()->getLogger()->error("Wystapil blad w QueryResult: ");
            return [];
        }

        $result->execute($params);
        if ($fetchAll) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        $fetchedResult = $result->fetch(PDO::FETCH_ASSOC);
        $this->getPlugin()->getLogger()->debug("Query result executed: " . $query);
        return $fetchedResult !== false ? $fetchedResult : [];
    }

    public function getQuery(string $query, array $params = []) : ?\PDOStatement {
        $result = $this->database->prepare($query);

        if ($result === false) {
            $this->getPlugin()->getLogger()->error("Wystapil blad w QueryResult: ");
            return null;
        }

        $result->execute($params);
        return $result;
    }

    public function close() : void {
        $this->database = null;
    }

    public function checkConnection() : bool {
        return $this->database === null;
    }
}