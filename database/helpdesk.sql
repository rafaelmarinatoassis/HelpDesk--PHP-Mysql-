-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS helpdesk
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE helpdesk;

-- Tabela de setores
CREATE TABLE setores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de tipos de usuário
CREATE TABLE tipos_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Inserir tipos de usuário padrão
INSERT INTO tipos_usuario (id, nome, descricao) VALUES
(1, 'Administrador', 'Acesso total ao sistema'),
(2, 'Técnico', 'Atende chamados'),
(3, 'Solicitante', 'Abre e acompanha chamados');

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    tipo_usuario_id INT NOT NULL,
    setor_id INT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    FOREIGN KEY (tipo_usuario_id) REFERENCES tipos_usuario(id),
    FOREIGN KEY (setor_id) REFERENCES setores(id)
);

-- Tabela de categorias de atendimento
CREATE TABLE categorias_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de status dos chamados
CREATE TABLE status_chamado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    cor VARCHAR(20) DEFAULT 'primary'
);

-- Inserir status padrão
INSERT INTO status_chamado (id, nome, descricao, cor) VALUES
(1, 'Aberto', 'Chamado registrado, aguardando atendimento', 'warning'),
(2, 'Em Atendimento', 'Chamado está sendo atendido por um técnico', 'info'),
(3, 'Aguardando Solicitante', 'Aguardando resposta ou ação do solicitante', 'secondary'),
(4, 'Resolvido', 'Problema resolvido, aguardando confirmação', 'success'),
(5, 'Fechado', 'Chamado concluído e fechado', 'dark');

-- Tabela de chamados
CREATE TABLE chamados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    solicitante_id INT NOT NULL,
    tecnico_id INT,
    categoria_id INT NOT NULL,
    setor_problema_id INT NOT NULL,
    status_id INT NOT NULL DEFAULT 1,
    prioridade ENUM('baixa', 'média', 'alta') DEFAULT 'média',
    solucao TEXT,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_ultima_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    data_fechamento DATETIME,
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias_atendimento(id),
    FOREIGN KEY (setor_problema_id) REFERENCES setores(id),
    FOREIGN KEY (status_id) REFERENCES status_chamado(id)
);

-- Tabela de histórico de chamados
CREATE TABLE chamados_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chamado_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo_alteracao ENUM('status', 'tecnico', 'comentario', 'solucao') NOT NULL,
    status_anterior INT,
    status_novo INT,
    tecnico_anterior INT,
    tecnico_novo INT,
    comentario TEXT,
    data_alteracao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chamado_id) REFERENCES chamados(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (status_anterior) REFERENCES status_chamado(id),
    FOREIGN KEY (status_novo) REFERENCES status_chamado(id),
    FOREIGN KEY (tecnico_anterior) REFERENCES usuarios(id),
    FOREIGN KEY (tecnico_novo) REFERENCES usuarios(id)
);

-- Inserir setor padrão para administração
INSERT INTO setores (id, nome, descricao) VALUES
(1, 'TI', 'Setor de Tecnologia da Informação'),
(2, 'Recursos Humanos', 'Setor responsável pela gestão de pessoas'),
(3, 'Financeiro', 'Setor responsável pelo controle financeiro e contábil'),
(4, 'Marketing', 'Setor responsável pela promoção e comunicação da empresa'),
(5, 'Vendas', 'Setor responsável pela comercialização de produtos ou serviços'),
(6, 'Logística', 'Setor responsável pelo transporte, armazenamento e distribuição'),
(7, 'Jurídico', 'Setor responsável por assuntos legais e jurídicos'),
(8, 'Administrativo', 'Setor responsável pela administração geral da empresa'),
(9, 'Operações', 'Setor responsável pela execução dos processos operacionais'),
(10, 'Atendimento ao Cliente', 'Setor responsável pelo suporte e atendimento aos clientes');

-- Inserir administrador padrão
-- Senha: admin123 (deve ser alterada no primeiro acesso)
INSERT INTO usuarios (nome_completo, email, senha_hash, tipo_usuario_id, setor_id) VALUES
('Administrador', 'admin@escola.com', '$2y$12$YM1zH0hOu.TCNrWxF0YVZu12xpmHs4W.LfLpxFxn5tNHDI.yAA9fS', 1, 1);

-- Criar algumas categorias padrão
INSERT INTO categorias_atendimento (nome, descricao) VALUES
('Hardware', 'Problemas com equipamentos físicos'),
('Software', 'Problemas com programas e sistemas'),
('Rede', 'Problemas de conexão e internet'),
('Impressora', 'Problemas com impressoras'),
('Projetor', 'Problemas com projetores'),
('Email', 'Problemas com email institucional'),
('Outros', 'Outros tipos de problemas');
