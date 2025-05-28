<?php
namespace Models;

/**
 * Classe Status
 * Gerencia os status dos chamados no sistema
 */
class Status extends Model {
    protected $table = 'status_chamado';
      /**
     * Busca todos os status ordenados
     */
    public function findAll(?string $orderBy = 'id', string $order = 'ASC'): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        return $this->db->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca um status específico
     */
    public function findById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca um status pelo nome
     */
    public function findByNome(string $nome) {
        $sql = "SELECT * FROM {$this->table} WHERE nome = :nome";
        return $this->db->execute($sql, ['nome' => $nome])->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtém a classe de estilo Bootstrap para o status
     */
    public static function getStatusBadgeClass(int $statusId): string {
        return match($statusId) {
            STATUS_ABERTO => 'warning',
            STATUS_EM_ATENDIMENTO => 'info',
            STATUS_AGUARDANDO_SOLICITANTE => 'secondary',
            STATUS_RESOLVIDO => 'success',
            STATUS_FECHADO => 'dark',
            default => 'primary'
        };
    }
    
    /**
     * Obtém o próximo status válido com base nas regras de negócio
     * @param int $statusAtual Status atual do chamado
     * @param int $tipoUsuario Tipo do usuário que está alterando o status
     * @return array Array com os status permitidos com id => nome
     */
    public function getProximosStatusPermitidos(int $statusAtual, int $tipoUsuario): array {
        $statusPermitidos = [];
        
        switch($statusAtual) {
            case STATUS_ABERTO:
                if ($tipoUsuario === USUARIO_TECNICO || $tipoUsuario === USUARIO_ADMIN) {
                    $statusPermitidos[STATUS_EM_ATENDIMENTO] = 'Em Atendimento';
                }
                break;
            
            case STATUS_EM_ATENDIMENTO:
                if ($tipoUsuario === USUARIO_TECNICO || $tipoUsuario === USUARIO_ADMIN) {
                    $statusPermitidos[STATUS_RESOLVIDO] = 'Resolvido';
                    $statusPermitidos[STATUS_AGUARDANDO_SOLICITANTE] = 'Aguardando Solicitante';
                }
                break;
            
            case STATUS_AGUARDANDO_SOLICITANTE:
                if ($tipoUsuario === USUARIO_SOLICITANTE) {
                    $statusPermitidos[STATUS_EM_ATENDIMENTO] = 'Em Atendimento';
                }
                break;
            
            case STATUS_RESOLVIDO:
                if ($tipoUsuario === USUARIO_SOLICITANTE) {
                    $statusPermitidos[STATUS_FECHADO] = 'Fechado';
                    $statusPermitidos[STATUS_EM_ATENDIMENTO] = 'Em Atendimento';
                }
                if ($tipoUsuario === USUARIO_TECNICO || $tipoUsuario === USUARIO_ADMIN) {
                    $statusPermitidos[STATUS_EM_ATENDIMENTO] = 'Em Atendimento';
                }
                break;
        }
        
        return $statusPermitidos;
    }
    
    /**
     * Verifica se a transição de status é válida
     */
    public function isTransicaoValida(int $statusAtual, int $novoStatus, int $tipoUsuario): bool {
        $statusPermitidos = $this->getProximosStatusPermitidos($statusAtual, $tipoUsuario);
        return array_key_exists($novoStatus, $statusPermitidos);
    }
}
