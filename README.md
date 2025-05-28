
---

## 🔑 Funcionalidades Principais

### 👤 Perfis de Usuário

O sistema conta com três tipos de usuários, cada um com suas permissões e funcionalidades específicas:

1.  **Administrador:** Controle total sobre o sistema.
2.  **Técnico:** Responsável por atender e solucionar os chamados.
3.  **Solicitante:** (Funcionários/Professores) Usuários que abrem e acompanham seus chamados.

### 💻 Solicitante (Funcionário/Professor)

*   **Dashboard:** Visão geral e acesso rápido para abrir chamados.
*   **Abrir Chamado:** Formulário intuitivo para registrar novas solicitações (Título, Descrição, Categoria, Setor do Problema).
*   **Meus Chamados:** Listagem e acompanhamento dos seus chamados, com filtros por status e período.
*   **Visualizar Detalhes:** Acesso completo às informações do chamado, incluindo histórico e solução.
*   **Atualizar Perfil:** Edição de dados pessoais e senha.

### 🔧 Técnico

*   **Dashboard:** Painel com chamados atribuídos e novos chamados aguardando atribuição.
*   **Meus Chamados Atribuídos:** Lista de chamados sob sua responsabilidade, com filtros avançados.
*   **Atender Chamado:**
    *   Visualização detalhada do problema.
    *   Atualização de status do chamado (Aberto, Em Atendimento, Resolvido, etc.).
    *   Registro da solução aplicada.
    *   Adição de comentários e atualizações (histórico).
*   **(Opcional) Assumir Chamado:** Capacidade de se auto-atribuir a chamados abertos.
*   **Atualizar Perfil:** Edição de dados pessoais e senha.

### 👑 Administrador

*   **Dashboard:** Visão geral completa do sistema com estatísticas e métricas (gráficos são um bônus).
*   **Gerenciamento de Usuários:** CRUD completo (Criar, Listar, Editar, Ativar/Desativar, Excluir) para todos os tipos de usuários.
*   **Gerenciamento de Categorias:** CRUD para categorias de atendimento.
*   **Gerenciamento de Setores:** CRUD para setores da escola.
*   **Gerenciamento de Chamados:**
    *   Visualização de TODOS os chamados com filtros avançados.
    *   Atribuição de chamados a técnicos.
    *   Edição completa de informações de um chamado.
    *   Exclusão de chamados (com confirmação).
*   **Relatórios:**
    *   Chamados por período, categoria, status, técnico.
    *   (Bônus) Tempo médio de resolução.
    *   Exportação para PDF (inicialmente, tabela HTML formatada para impressão).
*   **Atualizar Perfil:** Edição de dados pessoais e senha.

---

## 🛡️ Segurança

*   **Proteção contra XSS:** `htmlspecialchars()` é utilizado para sanitizar saídas.
*   **Proteção contra SQL Injection:** Todas as interações com o banco de dados são feitas via PDO com *Prepared Statements*.
*   **Senhas Seguras:** As senhas dos usuários são armazenadas utilizando `password_hash()` e verificadas com `password_verify()`.
*   **Controle de Acesso:** Verificação de permissões baseada no tipo de usuário logado para acesso às funcionalidades.

---

## ⚙️ Pré-requisitos

*   **XAMPP** (ou similar: WAMP, MAMP, LAMP) instalado e configurado.
    *   Apache
    *   MySQL
    *   PHP 7.4 ou superior (com as extensões PDO e pdo_mysql habilitadas)
*   Um navegador web moderno (Chrome, Firefox, Edge, Safari).
*   Um editor de código (VS Code, Sublime Text, PhpStorm, etc.).

---

## 🚀 Como Executar (Configuração Inicial)

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/rafaelmarinatoassis/HelpDesk--PHP-Mysql-.git
    cd SEU_REPOSITORIO
    ```
2.  **Configure o XAMPP:**
    *   Inicie os módulos Apache e MySQL no painel de controle do XAMPP.
    *   Coloque a pasta do projeto dentro do diretório `htdocs` do XAMPP (ex: `C:/xampp/htdocs/helpdesk-escolar`).
3.  **Crie o Banco de Dados:**
    *   Acesse o phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Crie um novo banco de dados (ex: `helpdesk_escolar_db`).
    *   Importe o arquivo `schema.sql` (que você fornecerá) para criar as tabelas e estrutura inicial.
4.  **Configure a Conexão:**
    *   Renomeie (se necessário) e edite o arquivo `config/db.example.php` para `config/db.php`.
    *   Atualize as credenciais do banco de dados (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) em `config/db.php`.
    *   Edite o arquivo `config/config.php` para definir a `BASE_URL` do seu projeto (ex: `define('BASE_URL', 'http://localhost/helpdesk-escolar/public');`).
5.  **Acesse o Sistema:**
    *   Abra seu navegador e acesse a `BASE_URL` definida (ex: `http://localhost/helpdesk-escolar/public`).
    *   Você deverá ver a página de login.

**Contas de Demonstração (Sugestão para `schema.sql`):**
*   **Administrador:** `admin@escola.com` / `senha123`
*   **Técnico:** `tecnico@escola.com` / `senha123`
*   **Solicitante:** `professor@escola.com` / `senha123`

---

## 🌊 Fluxo Básico de um Chamado

1.  **Solicitante** faz login e abre um novo chamado detalhando o problema.
2.  O chamado é registrado no sistema com status "Aberto".
3.  **Administrador** visualiza o novo chamado e o atribui a um **Técnico** disponível.
4.  **Técnico** recebe a notificação (ou visualiza em seu painel), analisa o chamado e atualiza seu status para "Em Atendimento".
5.  O **Técnico** trabalha na solução do problema.
6.  Após resolver, o **Técnico** registra a solução no sistema e atualiza o status para "Resolvido" (ou "Fechado").
7.  O **Solicitante** é notificado (ou visualiza em seu painel) que o chamado foi resolvido e pode ver a solução.
8.  O **Administrador** pode gerar relatórios incluindo este chamado para análises futuras.

---

## 📈 Roadmap (Próximos Passos)

O projeto está em desenvolvimento. As próximas etapas incluem:

1.  [ ] **Implementação da Estrutura Base:** Configuração inicial, `Database.php`, `Session.php`.
2.  [ ] **Autenticação e Modelos Iniciais:** `Usuario.php`, `Chamado.php`, tela de login e lógica.
3.  [ ] **Módulo Solicitante:** Implementação completa das funcionalidades do solicitante.
4.  [ ] **Módulo Técnico:** Implementação completa das funcionalidades do técnico.
5.  [ ] **Módulo Administrador:** Implementação das funcionalidades de gerenciamento (Usuários, Categorias, Setores).
6.  [ ] **Gerenciamento Avançado de Chamados (Admin):** Atribuição, edição, filtros.
7.  [ ] **Relatórios (Admin):** Implementação dos relatórios básicos.
8.  [ ] **Refinamento da Interface:** Melhorias visuais e de usabilidade com Bootstrap.
9.  [ ] **Testes Unitários e de Integração:** Garantir a qualidade e estabilidade do código.
10. [ ] **(Bônus) Notificações por Email:** Para atualizações importantes nos chamados.
11. [ ] **(Bônus) Anexar Arquivos:** Permitir que solicitantes e técnicos anexem arquivos aos chamados.
12. [ ] **(Bônus) Exportação de Relatórios para PDF/Excel:** Utilizando bibliotecas como FPDF/TCPDF ou PhpSpreadsheet.

---

## 🤝 Contribuição

Contribuições são bem-vindas! Se você tem alguma ideia para melhorar o projeto, siga os passos:

1.  Faça um **Fork** do projeto.
2.  Crie uma nova **Branch** (`git checkout -b feature/sua-feature`).
3.  Faça suas alterações e **Commit** (`git commit -m 'Adiciona nova feature incrível'`).
4.  Envie para a sua Branch (`git push origin feature/sua-feature`).
5.  Abra um **Pull Request**.

Por favor, certifique-se de que seu código segue os padrões do projeto e inclua testes quando aplicável.

---

## 📝 Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo `LICENSE` para mais detalhes (você precisará criar este arquivo se quiser formalizar a licença).

---

Feito com ❤️ e muito ☕ por Rafael Marinato Assis
