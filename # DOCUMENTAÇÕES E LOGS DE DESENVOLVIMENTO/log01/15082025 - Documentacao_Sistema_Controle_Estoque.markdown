# Documentação Técnica do Sistema de Controle de Estoque IGA

**Versão 1.0 - 15 de Agosto de 2025**  
**Última Atualização: 05-08-2025 14:30**  
**Desenvolvido para Gestão de Unidades de Saúde**

## 1. Introdução

Este documento apresenta a documentação técnica completa do Sistema de Controle de Estoque IGA, uma aplicação web projetada para gerenciar estoques em unidades de saúde, com foco em rastreabilidade por lote, controle de entradas e saídas e geração de relatórios gerenciais. O sistema está em fase inicial de desenvolvimento (30% completo) e é voltado para gestores de unidades de saúde.

### 1.1. Histórico de Desenvolvimento
- **Data da Última Atualização**: 05-08-2025, 14:30.
- **Log Anterior**: 05-08-2025, 10:15, sem duplicações significativas.
- **Versão Atual**: Fase inicial, com autenticação, recuperação de senha e CRUD de usuários implementados.

### 1.2. Escopo Geral
O sistema abrange:
- Controle de estoque com rastreabilidade por lote e validade.
- Gestão de entradas (notas fiscais) e saídas (requisições).
- Relatórios gerenciais (inventário, Kardex, Curva ABC, custos).
- Integração com e-mails para notificações.
- Design responsivo para desktop, tablet e smartphone.

## 2. Objetivos e Público-Alvo

### 2.1. Objetivos Principais
- Monitoramento e gerenciamento de estoque em tempo real.
- Rastreabilidade completa de produtos por lote.
- Automação de processos de entrada/saída.
- Relatórios analíticos para tomada de decisões.
- Segurança robusta para dados sensíveis.

### 2.2. Público-Alvo
- Gestores de unidades de saúde (hospitais, clínicas, depósitos).
- Usuários operacionais (entradas/saídas).
- Supervisores e auditores (relatórios e rastreabilidade).

## 3. Tecnologias Utilizadas

### 3.1. Frontend
- HTML5, CSS3, JavaScript (Vanilla).
- Bootstrap 5.3.0: Responsividade e componentes visuais.
- Font Awesome 6.0.0: Ícones vetoriais.

### 3.2. Backend
- PHP 8.2+: Linguagem principal com PDO.
- Arquitetura MVC: Separação de lógica, dados e apresentação.

### 3.3. Banco de Dados
- MySQL 8.0: Banco relacional com chaves estrangeiras.
- Nomeclatura em português (requisito do cliente).
- Script: `/sql/estrutura.sql`.

### 3.4. Bibliotecas e Ferramentas
- PHPMailer: Envio de e-mails.
- DomPDF: Geração de relatórios (planejado).
- Composer: Gerenciamento de dependências.

## 4. Estrutura de Diretórios

- **/app**
  - **/controllers**: `autenticacao.php`, `usuario_controller.php`, `produto_controller.php`, `unidades_saude/salvar.php`, `usuarios/salvar.php`.
  - **/models**: `Usuario.php`, `Produto.php` (parcial).
  - **/views**: Subpastas `usuarios`, `produtos`, `unidades_saude`, arquivos `login.php`, `dashboard.php`, `recuperar_senha.php`, `redefinir_senha.php`.
- **/config**: `conexao.php`, `config.php`.
- **/assets**: `/css/style.css`, `/img/img_logo.png`, `/js` (planejado).
- **/public**: `index.php` (roteador).
- **/sql**: `estrutura.sql`.
- **/vendor**: PHPMailer (via Composer).

## 5. Estrutura do Banco de Dados

Banco: `iga_bd`. Estruturado para rastreabilidade e integridade.

### 5.1. Tabelas Principais
- `usuarios`: Dados de usuários (ID, nome, login, senha, papel).
- `usuariopapeis`: Papéis (ex.: admin, gestor).
- `produtos`: Detalhes de produtos.
- `categorias`, `unidades_saude`, `fornecedores`, `entradas_estoque`, `entradas_itens`, `requisicoes`, `requisicoes_itens`, `tokens_recuperacao`.

### 5.2. Relações
- Chaves estrangeiras com `ON DELETE RESTRICT`.
- Exemplo: `usuarios.papel_id` ligado a `usuariopapeis`.

## 6. Funcionalidades Implementadas

### 6.1. Autenticação e Segurança
- Login com `password_verify()`.
- Controle de sessão via `$_SESSION`.
- Logout seguro.
- Recuperação de senha com token e PHPMailer.

### 6.2. CRUD de Entidades
- **Usuários**: Cadastro, edição, listagem com paginação, exclusão.
- **Unidades de Saúde**: CRUD completo.
- **Produtos**: Listagem parcial.

### 6.3. Dashboard
- Cards de resumo (mockados).
- Tabela de produtos.
- Menu lateral.

### 6.4. Outras
- Entrada de produtos (em andamento).
- Requisição de saída com PDF.

## 7. Decisões Técnicas

### 7.1. Segurança
- Hash de senhas com `password_hash()`.
- Prepared statements via PDO.
- Tokens CSRF (planejado).

### 7.2. Padrões de Código
- Variáveis em português.
- Indentação: 4 espaços.
- Chaves na mesma linha.

### 7.3. Banco de Dados
- Nomeclatura em português.
- Tabela `tokens_recuperacao` para senhas.

## 8. Exemplos de Códigos Fonte

### 8.设计师1. Controlador de Autenticação (`autenticacao.php`)

```php
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['username'] ?? '');
    $senha = $_POST['password'] ?? '';

    if (empty($usuario) || empty($senha)) {
        $_SESSION['erro_login'] = "Usuário e senha são obrigatórios.";
        header('Location: ../../app/views/login.php');
        exit;
    }

    try {
        $sql = "SELECT u.*, p.usuariopapel_nome 
                FROM usuarios u
                JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                WHERE u.usuario_login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $usuario);
        $stmt->execute();
        $dados = $stmt->fetch();

        if ($dados && password_verify($senha, $dados['usuario_senha'])) {
            $_SESSION['usuario_id'] = $dados['usuario_id'];
            $_SESSION['usuario_nome'] = $dados['usuario_nome'];
            $_SESSION['usuario_papel'] = $dados['usuariopapel_nome'];
            header('Location: ../../app/views/dashboard.php');
            exit;
        } else {
            $_SESSION['erro_login'] = "Credenciais inválidas.";
            header('Location: ../../app/views/login.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = "Erro no sistema.";
        header('Location: ../../app/views/login.php');
        exit;
    }
} else {
    header('Location: ../../app/views/login.php');
    exit;
}
```

### 8.2. Modelo de Usuário (`Usuario.php`)

```php
<?php
class Usuario {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function listarTodos() {
        $sql = "SELECT u.*, p.usuariopapel_nome 
                FROM usuarios u 
                JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id";
        return $this->pdo->query($sql)->fetchAll();
    }
    // ... (outros métodos mantidos conforme versão anterior)
}
```

## 9. Próximos Passos Prioritários

1. **CRUD de Produtos**: Finalizar formulários e upload de imagens.
2. **Entrada de Produtos**: Notas fiscais, controle por lote/validade.
3. **Permissões**: RBAC com middleware.
4. **Dashboard Dinâmico**: Gráficos e alertas.
5. **Outros**: CRUD de categorias/fornecedores, relatórios, configurações.

## 10. Observações e Pendências

### 10.1. Pendências
- Validação de força de senha.
- Log de atividades (auditoria).
- Paginação em listagens.
- Tokens CSRF.
- Integração completa de DomPDF.

### 10.2. Considerações Gerais
- Foco em usabilidade e segurança para saúde.
- Atualizações incluirão testes unitários.
- Recomendações: Backups e monitoramento de erros.

## 11. Considerações Finais

O Sistema IGA é uma solução robusta para controle de estoque, com potencial de expansão. A documentação será atualizada conforme o progresso. Sugestões devem ser registradas no log de projeto.