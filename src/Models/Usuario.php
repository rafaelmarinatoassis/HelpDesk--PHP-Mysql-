<?php
namespace Models;

use Core\Database;

/**
 * Classe Usuario
 * Gerencia os dados e operações relacionadas aos usuários do sistema
 */
class Usuario extends Model {
    protected $table = 'usuarios';
    protected $useSoftDelete = true;
    
    // Constantes para tipos de usuário
    public const TIPO_ADMIN = USUARIO_ADMIN;
    public const TIPO_TECNICO = USUARIO_TECNICO;
    public const TIPO_SOLICITANTE = USUARIO_SOLICITANTE;
    
    /**
     * Busca um usuário pelo email
     */
    public function findByEmail(string $email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND ativo = true";
        $stmt = $this->db->execute($sql, ['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica se a senha está correta
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Cria um novo usuário
     */
    public function create(array $data): int {
        if (isset($data['senha'])) {
            $data['senha_hash'] = password_hash($data['senha'], PASSWORD_DEFAULT, ['cost' => HASH_COST]);
            unset($data['senha']);
        }
        
        $data['ativo'] = $data['ativo'] ?? true;
        $data['data_criacao'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza um usuário
     */
    public function update(int $id, array $data) {
        if (isset($data['senha'])) {
            $data['senha_hash'] = password_hash($data['senha'], PASSWORD_DEFAULT, ['cost' => HASH_COST]);
            unset($data['senha']);
        }
        
        $data['data_atualizacao'] = date('Y-m-d H:i:s');
        
        return parent::update($id, $data);
    }
    
    /**
     * Busca usuários por tipo
     */
    public function findByTipo(int $tipoUsuarioId) {
        $sql = "SELECT * FROM {$this->table} WHERE tipo_usuario_id = :tipo AND ativo = true ORDER BY nome_completo";
        $stmt = $this->db->execute($sql, ['tipo' => $tipoUsuarioId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca usuários por setor
     */
    public function findBySetor(int $setorId) {
        $sql = "SELECT * FROM {$this->table} WHERE setor_id = :setor AND ativo = true ORDER BY nome_completo";
        $stmt = $this->db->execute($sql, ['setor' => $setorId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Lista todos os técnicos ativos
     */
    public function findTecnicos() {
        return $this->findByTipo(self::TIPO_TECNICO);
    }
    
    /**
     * Desativa um usuário (soft delete)
     */
    public function desativar(int $id): bool {
        return $this->update($id, ['ativo' => false]);
    }
    
    /**
     * Ativa um usuário
     */
    public function ativar(int $id): bool {
        return $this->update($id, ['ativo' => true]);
    }
}
