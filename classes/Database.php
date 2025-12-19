<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // #region agent log
        @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-construct','hypothesisId'=>'D','location'=>'Database.php:9','message'=>'Database constructor called','data'=>['globals_pdo_exists'=>isset($GLOBALS['pdo']),'globals_pdo_is_null'=>$GLOBALS['pdo']??null===null],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        global $pdo;
        
        // #region agent log
        @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-construct','hypothesisId'=>'D','location'=>'Database.php:14','message'=>'After global declaration','data'=>['pdo_exists'=>isset($pdo),'pdo_is_null'=>$pdo??null===null,'globals_pdo_exists'=>isset($GLOBALS['pdo'])],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        // Try to get from globals if not in local scope
        if (!isset($pdo) || $pdo === null) {
            if (isset($GLOBALS['pdo'])) {
                $pdo = $GLOBALS['pdo'];
                // #region agent log
                @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-construct','hypothesisId'=>'D','location'=>'Database.php:20','message'=>'Retrieved pdo from GLOBALS','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                // #endregion
            } else {
                // #region agent log
                @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-construct','hypothesisId'=>'D','location'=>'Database.php:25','message'=>'PDO not found in global scope','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                // #endregion
                throw new Exception('Database connection not initialized. Please check your database configuration.');
            }
        }
        $this->pdo = $pdo;
        
        // #region agent log
        @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-construct','hypothesisId'=>'D','location'=>'Database.php:32','message'=>'Database constructor completed','data'=>['pdo_set'=>isset($this->pdo)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
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
