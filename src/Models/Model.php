<?php
namespace Models;

use Core\Database;
use PDO;

/**
 * Classe Model Base
 * Implementa funcionalidades básicas comuns a todos os models
 */
abstract class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Busca um registro pelo ID
     */
    public function findById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->execute($sql, ['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca todos os registros
     */
    public function findAll(string $orderBy = 'id', string $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}";
        $stmt = $this->db->execute($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insere um novo registro
     */
    public function create(array $data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $this->db->execute($sql, $data);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Atualiza um registro
     */
    public function update(int $id, array $data) {
        $fields = array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;
        
        return $this->db->execute($sql, $data);
    }
    
    /**
     * Exclui um registro (soft delete se aplicável)
     */
    public function delete(int $id) {
        if (property_exists($this, 'useSoftDelete') && $this->useSoftDelete) {
            return $this->update($id, ['ativo' => false]);
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }
}
