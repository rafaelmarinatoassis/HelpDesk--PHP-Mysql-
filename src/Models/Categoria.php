<?php
namespace Models;

/**
 * Classe Categoria
 * Gerencia as categorias de atendimento do sistema
 */
class Categoria extends Model {
    protected $table = 'categorias_atendimento';
    protected $useSoftDelete = true;
    
    /**
     * Busca categorias ativas
     */
    public function findAtivas() {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = true ORDER BY nome";
        return $this->db->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica se uma categoria está em uso
     */
    public function estaEmUso(int $id): bool {
        $sql = "SELECT COUNT(*) as total FROM chamados WHERE categoria_id = :id";
        $result = $this->db->execute($sql, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    /**
     * Cria uma nova categoria
     */
    public function create(array $data): int {
        $data['ativo'] = $data['ativo'] ?? true;
        $data['data_criacao'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza uma categoria
     */
    public function update(int $id, array $data) {
        $data['data_atualizacao'] = date('Y-m-d H:i:s');
        
        return parent::update($id, $data);
    }
    
    /**
     * Desativa uma categoria
     */
    public function desativar(int $id): bool {
        if ($this->estaEmUso($id)) {
            throw new \Exception('Não é possível desativar uma categoria em uso');
        }
        
        return $this->update($id, ['ativo' => false]);
    }
    
    /**
     * Ativa uma categoria
     */
    public function ativar(int $id): bool {
        return $this->update($id, ['ativo' => true]);
    }
    
    /**
     * Obtém estatísticas de uso das categorias
     */
    public function getEstatisticas() {
        $sql = "SELECT 
                    c.id,
                    c.nome,
                    COUNT(ch.id) as total_chamados,
                    SUM(CASE WHEN ch.status_id = :status_aberto THEN 1 ELSE 0 END) as abertos,
                    SUM(CASE WHEN ch.status_id = :status_andamento THEN 1 ELSE 0 END) as em_andamento,
                    SUM(CASE WHEN ch.status_id = :status_resolvido THEN 1 ELSE 0 END) as resolvidos
                FROM {$this->table} c
                LEFT JOIN chamados ch ON c.id = ch.categoria_id
                WHERE c.ativo = true
                GROUP BY c.id, c.nome
                ORDER BY total_chamados DESC";
        
        return $this->db->execute($sql, [
            'status_aberto' => STATUS_ABERTO,
            'status_andamento' => STATUS_EM_ATENDIMENTO,
            'status_resolvido' => STATUS_RESOLVIDO
        ])->fetchAll(\PDO::FETCH_ASSOC);
    }
}
