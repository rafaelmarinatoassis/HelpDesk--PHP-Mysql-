<?php

namespace Models;

use Core\Database;

/**
 * Classe Chamado
 * Gerencia os dados e operações relacionadas aos chamados do sistema
 */
class Chamado extends Model
{
    protected $table = 'chamados';

    /**
     * Cria um novo chamado
     */
    public function create(array $data): int
    {
        $data['data_abertura'] = date('Y-m-d H:i:s');
        $data['data_ultima_atualizacao'] = $data['data_abertura'];
        $data['status_id'] = STATUS_ABERTO;

        return parent::create($data);
    }

    /**
     * Atualiza um chamado
     */
    public function update(int $id, array $data)
    {
        $data['data_ultima_atualizacao'] = date('Y-m-d H:i:s');

        // Se estiver mudando para resolvido ou fechado, adiciona data de fechamento
        if (
            isset($data['status_id']) &&
            in_array($data['status_id'], [STATUS_RESOLVIDO, STATUS_FECHADO])
        ) {
            $data['data_fechamento'] = date('Y-m-d H:i:s');
        }

        return parent::update($id, $data);
    }

    /**
     * Busca um chamado pelo ID com todas as informações relacionadas
     */
    public function findById(int $id)
    {
        $sql = "SELECT c.*, u.nome_completo as solicitante_nome, t.nome_completo as tecnico_nome, 
                       cat.nome as categoria_nome, s.nome as status_nome, st.nome as setor_nome,
                       c.data_abertura, c.data_ultima_atualizacao
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                LEFT JOIN setores st ON c.setor_problema_id = st.id
                WHERE c.id = :id";

        $stmt = $this->db->execute($sql, ['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca chamados por solicitante
     */
    public function findBySolicitante(int $solicitanteId)
    {
        $sql = "SELECT c.*, u.nome_completo as solicitante, t.nome_completo as tecnico, 
                       cat.nome as categoria, s.nome as status, st.nome as setor,
                       c.data_abertura, c.data_ultima_atualizacao
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                LEFT JOIN setores st ON c.setor_problema_id = st.id
                WHERE c.solicitante_id = :solicitante_id
                ORDER BY c.data_abertura DESC";

        $stmt = $this->db->execute($sql, ['solicitante_id' => $solicitanteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca chamados por técnico
     */
    public function findByTecnico(int $tecnicoId)
    {
        $sql = "SELECT c.*, u.nome_completo as solicitante, t.nome_completo as tecnico, 
                       cat.nome as categoria, s.nome as status, st.nome as setor
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                LEFT JOIN setores st ON c.setor_problema_id = st.id
                WHERE c.tecnico_id = :tecnico_id
                ORDER BY c.prioridade DESC, c.data_abertura ASC";

        $stmt = $this->db->execute($sql, ['tecnico_id' => $tecnicoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca chamados por status
     */
    public function findByStatus(int $statusId)
    {
        $sql = "SELECT c.*, u.nome_completo as solicitante, t.nome_completo as tecnico, 
                       cat.nome as categoria, s.nome as status, st.nome as setor
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                LEFT JOIN setores st ON c.setor_problema_id = st.id
                WHERE c.status_id = :status_id
                ORDER BY c.data_abertura ASC";

        $stmt = $this->db->execute($sql, ['status_id' => $statusId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca chamados por período
     */
    public function findByPeriodo(string $dataInicio, string $dataFim)
    {
        $sql = "SELECT c.*, u.nome_completo as solicitante, t.nome_completo as tecnico, 
                       cat.nome as categoria, s.nome as status, st.nome as setor
                FROM {$this->table} c
                LEFT JOIN usuarios u ON c.solicitante_id = u.id
                LEFT JOIN usuarios t ON c.tecnico_id = t.id
                LEFT JOIN categorias_atendimento cat ON c.categoria_id = cat.id
                LEFT JOIN status_chamado s ON c.status_id = s.id
                LEFT JOIN setores st ON c.setor_problema_id = st.id
                WHERE c.data_abertura BETWEEN :data_inicio AND :data_fim
                ORDER BY c.data_abertura DESC";

        $stmt = $this->db->execute($sql, [
            'data_inicio' => $dataInicio . ' 00:00:00',
            'data_fim' => $dataFim . ' 23:59:59'
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Atribui um técnico ao chamado
     */
    public function atribuirTecnico(int $chamadoId, int $tecnicoId): bool
    {
        return $this->update($chamadoId, [
            'tecnico_id' => $tecnicoId,
            'status_id' => STATUS_EM_ATENDIMENTO
        ]);
    }

    /**
     * Registra a solução de um chamado
     */
    public function registrarSolucao(int $chamadoId, string $solucao): bool
    {
        return $this->update($chamadoId, [
            'solucao' => $solucao,
            'status_id' => STATUS_RESOLVIDO
        ]);
    }
}
