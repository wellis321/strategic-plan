<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        global $pdo;
        
        // Try to get from globals if not in local scope
        if (!isset($pdo) || $pdo === null) {
            if (isset($GLOBALS['pdo'])) {
                $pdo = $GLOBALS['pdo'];
            } else {
                throw new Exception('Database connection not initialized. Please check your database configuration.');
            }
        }
        $this->pdo = $pdo;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Check if error is due to missing table
            if ($e->getCode() === '42S02' || strpos($e->getMessage(), "doesn't exist") !== false) {
                error_log('Database table missing: ' . $e->getMessage());
                throw new Exception('Database tables not initialized. Please import the database schema.', 0, $e);
            }
            // Re-throw other PDO exceptions
            throw $e;
        }
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);

        // Merge data and where parameters
        $params = array_merge($data, $whereParams);
        return $stmt->execute($params);
    }

    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($whereParams);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }
}
?>
