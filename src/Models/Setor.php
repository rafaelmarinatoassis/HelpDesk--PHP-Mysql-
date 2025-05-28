<?php
namespace Models;

/**
 * Classe Setor
 * Gerencia os setores da instituição
 */
class Setor extends Model {
    protected $table = 'setores';
    protected $useSoftDelete = true;
    
    /**
     * Busca setores ativos
     */
    public function findAtivos() {
        $sql = "SELECT * FROM {$this->table} WHERE ativo = true ORDER BY nome";
        return $this->db->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica se um setor está em uso (por usuários ou chamados)
     */
    public function estaEmUso(int $id): bool {
        // Verifica se há usuários no setor
        $sqlUsuarios = "SELECT COUNT(*) as total FROM usuarios WHERE setor_id = :id";
        $resultUsuarios = $this->db->execute($sqlUsuarios, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
        
        // Verifica se há chamados para o setor
        $sqlChamados = "SELECT COUNT(*) as total FROM chamados WHERE setor_problema_id = :id";
        $resultChamados = $this->db->execute($sqlChamados, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
        
        return ($resultUsuarios['total'] + $resultChamados['total']) > 0;
    }
    
    /**
     * Cria um novo setor
     */
    public function create(array $data): int {
        $data['ativo'] = $data['ativo'] ?? true;
        $data['data_criacao'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza um setor
     */
    public function update(int $id, array $data) {
        $data['data_atualizacao'] = date('Y-m-d H:i:s');
        
        return parent::update($id, $data);
    }
    
    /**
     * Desativa um setor
     */
    public function desativar(int $id): bool {
        if ($this->estaEmUso($id)) {
            throw new \Exception('Não é possível desativar um setor em uso');
        }
        
        return $this->update($id, ['ativo' => false]);
    }
    
    /**
     * Ativa um setor
     */
    public function ativar(int $id): bool {
        return $this->update($id, ['ativo' => true]);
    }
    
    /**
     * Obtém estatísticas de chamados por setor
     */
    public function getEstatisticas() {
        $sql = "SELECT 
                    s.id,
                    s.nome,
                    COUNT(ch.id) as total_chamados,
                    SUM(CASE WHEN ch.status_id = :status_aberto THEN 1 ELSE 0 END) as abertos,
                    SUM(CASE WHEN ch.status_id = :status_andamento THEN 1 ELSE 0 END) as em_andamento,
                    SUM(CASE WHEN ch.status_id = :status_resolvido THEN 1 ELSE 0 END) as resolvidos,
                    COUNT(DISTINCT u.id) as total_usuarios,
                    COUNT(DISTINCT CASE WHEN u.tipo_usuario_id = :tipo_tecnico THEN u.id END) as total_tecnicos
                FROM {$this->table} s
                LEFT JOIN chamados ch ON s.id = ch.setor_problema_id
                LEFT JOIN usuarios u ON s.id = u.setor_id
                WHERE s.ativo = true
                GROUP BY s.id, s.nome
                ORDER BY total_chamados DESC";
        
        return $this->db->execute($sql, [
            'status_aberto' => STATUS_ABERTO,
            'status_andamento' => STATUS_EM_ATENDIMENTO,
            'status_resolvido' => STATUS_RESOLVIDO,
            'tipo_tecnico' => USUARIO_TECNICO
        ])->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém os usuários de um setor
     */
    public function getUsuarios(int $id) {
        $sql = "SELECT 
                    u.*,
                    t.nome as tipo_usuario
                FROM usuarios u
                JOIN tipos_usuario t ON u.tipo_usuario_id = t.id
                WHERE u.setor_id = :id AND u.ativo = true
                ORDER BY u.nome_completo";
                
        return $this->db->execute($sql, ['id' => $id])->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém os chamados de um setor
     */
    public function getChamados(int $id) {
        $sql = "SELECT 
                    c.*,
                    u.nome_completo as solicitante,
                    t.nome_completo as tecnico,
                    cat.nome as categoria,
                    s.nome as status
                FROM chamados c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                WHERE c.setor_problema_id = :id
                ORDER BY c.data_abertura DESC";
                
        return $this->db->execute($sql, ['id' => $id])->fetchAll(\PDO::FETCH_ASSOC);
    }
}
