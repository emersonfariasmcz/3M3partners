<?php
# ========================================
# Model Cliente
# Local: /app/models/cliente.php
# ========================================

class cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os clientes com informações do estado.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT c.*, e.estado_nome, e.estado_uf
                FROM clientes c
                LEFT JOIN estados e ON c.cliente_estado_id = e.estado_id
                ORDER BY c.cliente_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um cliente pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM clientes WHERE cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo cliente.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO clientes
                (cliente_nome, cliente_nome_fantasia, cliente_cnpj_cpf, cliente_inscricao_estadual,
                 cliente_endereco, cliente_bairro, cliente_cidade, cliente_estado_id, cliente_cep,
                 cliente_telefone, cliente_telefone_secundario, cliente_email, cliente_contato_principal,
                 cliente_observacoes, cliente_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['nome_fantasia'] ?? null),
            !empty($dados['cnpj_cpf']) ? preg_replace('/\D/', '', $dados['cnpj_cpf']) : null,
            trim($dados['inscricao_estadual'] ?? null),
            trim($dados['endereco'] ?? null),
            trim($dados['bairro'] ?? null),
            trim($dados['cidade'] ?? null),
            !empty($dados['estado_id']) ? (int)$dados['estado_id'] : null,
            trim($dados['cep'] ?? null),
            trim($dados['telefone'] ?? null),
            trim($dados['telefone_secundario'] ?? null),
            trim($dados['email'] ?? null),
            trim($dados['contato_principal'] ?? null),
            trim($dados['observacoes'] ?? null),
            $dados['status'] ?? 'ativo'
        ]);
    }

    /**
     * Atualiza um cliente existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE clientes SET
                cliente_nome = ?, cliente_nome_fantasia = ?, cliente_cnpj_cpf = ?, cliente_inscricao_estadual = ?,
                cliente_endereco = ?, cliente_bairro = ?, cliente_cidade = ?, cliente_estado_id = ?, cliente_cep = ?,
                cliente_telefone = ?, cliente_telefone_secundario = ?, cliente_email = ?, cliente_contato_principal = ?,
                cliente_observacoes = ?, cliente_status = ?
                WHERE cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['nome_fantasia'] ?? null),
            !empty($dados['cnpj_cpf']) ? preg_replace('/\D/', '', $dados['cnpj_cpf']) : null,
            trim($dados['inscricao_estadual'] ?? null),
            trim($dados['endereco'] ?? null),
            trim($dados['bairro'] ?? null),
            trim($dados['cidade'] ?? null),
            !empty($dados['estado_id']) ? (int)$dados['estado_id'] : null,
            trim($dados['cep'] ?? null),
            trim($dados['telefone'] ?? null),
            trim($dados['telefone_secundario'] ?? null),
            trim($dados['email'] ?? null),
            trim($dados['contato_principal'] ?? null),
            trim($dados['observacoes'] ?? null),
            $dados['status'] ?? 'ativo',
            $id
        ]);
    }

    /**
     * Exclui um cliente.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verificar se está sendo usado por alguma OS/Pedido (opcional)
        // $sqlCheck = "SELECT COUNT(*) FROM pedidosvenda WHERE cliente_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM clientes WHERE cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CNPJ/CPF já existe (excluindo o cliente com $idExcluir).
     *
     * @param string $cnpj_cpf
     * @param int|null $idExcluir
     * @return bool
     */
    public function cnpjCpfExiste($cnpj_cpf, $idExcluir = null) {
        if (empty($cnpj_cpf)) {
            return false;
        }
        $cnpj_cpf_limpo = preg_replace('/\D/', '', $cnpj_cpf);
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM clientes WHERE cliente_cnpj_cpf = ? AND cliente_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpj_cpf_limpo, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM clientes WHERE cliente_cnpj_cpf = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpj_cpf_limpo]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o E-mail já existe (excluindo o cliente com $idExcluir).
     *
     * @param string $email
     * @param int|null $idExcluir
     * @return bool
     */
    public function emailExiste($email, $idExcluir = null) {
        if (empty($email)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM clientes WHERE cliente_email = ? AND cliente_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM clientes WHERE cliente_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // Métodos auxiliares para dropdowns

    /**
     * Busca todos os estados.
     *
     * @return array
     */
    public function listarEstados() {
        $sql = "SELECT estado_id, estado_nome, estado_uf FROM estados ORDER BY estado_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>