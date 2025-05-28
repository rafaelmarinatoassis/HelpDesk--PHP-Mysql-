<?php
namespace Core;

use PDO;
use PDOException;

/**
 * Classe Database
 * Gerencia a conexão e operações com o banco de dados usando PDO
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Construtor privado para implementar Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . 
                   ";dbname=" . DB_NAME . 
                   ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Erro de conexão: " . $e->getMessage());
            } else {
                die("Erro ao conectar ao banco de dados.");
            }
        }
    }
    
    /**
     * Obtém a instância única da conexão (Singleton)
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtém a conexão PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Prepara e executa uma query
     * @param string $sql Query SQL
     * @param array $params Parâmetros para bind
     * @return \PDOStatement
     */
    public function execute(string $sql, array $params = []): \PDOStatement {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Erro na query: " . $e->getMessage());
            } else {
                die("Erro ao executar operação no banco de dados.");
            }
        }
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Reverte uma transação
     */
    public function rollback(): bool {
        return $this->connection->rollBack();
    }
    
    /**
     * Retorna o último ID inserido
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Impede que a classe seja clonada
     */
    private function __clone() {}
      /**
     * Impede que a classe seja deserializada
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
