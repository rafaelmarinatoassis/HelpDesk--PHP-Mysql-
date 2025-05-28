<?php

namespace Controllers;

use Core\Session;
use Models\Chamado;
use Models\Usuario;
use Models\Categoria;
use Models\Setor;

/**
 * Controlador de Chamados
 * Gerencia as operações relacionadas aos chamados do sistema
 */
class ChamadoController
{
    private $chamadoModel;
    private $usuarioModel;
    private $categoriaModel;
    private $setorModel;

    public function __construct()
    {
        $this->chamadoModel = new Chamado();
        $this->usuarioModel = new Usuario();
        $this->categoriaModel = new Categoria();
        $this->setorModel = new Setor();
    }

    /**
     * Busca todos os chamados de um solicitante
     */
    public function getChamadosBySolicitante(int $solicitanteId)
    {
        return $this->chamadoModel->findBySolicitante($solicitanteId);
    }

    /**
     * Busca um chamado pelo ID
     */
    public function getChamado(int $id)
    {
        return $this->chamadoModel->findById($id);
    }

    /**
     * Cria um novo chamado
     */
    public function criarChamado(array $data): bool
    {
        try {
            // Valida dados obrigatórios
            if (empty($data['titulo']) || empty($data['descricao']) || empty($data['categoria_id'])) {
                return false;
            }

            // Obtém o ID do solicitante da sessão
            $solicitanteId = Session::getUsuarioId();

            // Obtém o setor do usuário solicitante
            $solicitante = $this->usuarioModel->findById($solicitanteId);
            if (!$solicitante) {
                return false;
            }
            // Prepara os dados do chamado
            $chamadoData = [
                'titulo' => trim($data['titulo']),
                'descricao' => trim($data['descricao']),
                'solicitante_id' => $solicitanteId,
                'categoria_id' => (int)$data['categoria_id'],
                'setor_problema_id' => (int)$data['setor_destino_id'], // setor para onde o chamado será enviado
                'prioridade' => 'normal', // prioridade padrão
                'status_id' => STATUS_ABERTO // status inicial
            ];

            // Cria o chamado
            return $this->chamadoModel->create($chamadoData) > 0;
        } catch (\Exception $e) {
            // Em produção, você deve logar o erro apropriadamente
            return false;
        }
    }
}
