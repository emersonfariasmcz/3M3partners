<?php
# ========================================
# Model Vendedor
# Local: /app/models/vendedor.php
# ========================================

class vendedor {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os vendedores com informações do supervisor (se houver).
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT v.*, s.vendedor_nome AS supervisor_nome
                FROM vendedores v
                LEFT JOIN vendedores s ON v.vendedor_supervisor_id = s.vendedor_id
                ORDER BY v.vendedor_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um vendedor pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM vendedores WHERE vendedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo vendedor.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO vendedores
                (vendedor_nome, vendedor_matricula, vendedor_cpf, vendedor_endereco,
                 vendedor_bairro, vendedor_cidade, vendedor_estado_id, vendedor_cep,
                 vendedor_telefone, vendedor_email, vendedor_comissao_percentual,
                 vendedor_data_admissao, vendedor_supervisor_id, vendedor_observacoes, vendedor_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['matricula'] ?? null),
            !empty($dados['cpf']) ? preg_replace('/\D/', '', $dados['cpf']) : null, // Remove formatação
            trim($dados['endereco'] ?? null),
            trim($dados['bairro'] ?? null),
            trim($dados['cidade'] ?? null),
            !empty($dados['estado_id']) ? (int)$dados['estado_id'] : null,
            trim($dados['cep'] ?? null),
            trim($dados['telefone'] ?? null),
            trim($dados['email'] ?? null),
            (float)($dados['comissao_percentual'] ?? 0.00),
            !empty($dados['data_admissao']) ? date('Y-m-d', strtotime($dados['data_admissao'])) : null, // Formato YYYY-MM-DD
            !empty($dados['supervisor_id']) ? (int)$dados['supervisor_id'] : null,
            trim($dados['observacoes'] ?? null),
            $dados['status'] ?? 'ativo'
        ]);
    }

    /**
     * Atualiza um vendedor existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE vendedores SET
                vendedor_nome = ?, vendedor_matricula = ?, vendedor_cpf = ?, vendedor_endereco = ?,
                vendedor_bairro = ?, vendedor_cidade = ?, vendedor_estado_id = ?, vendedor_cep = ?,
                vendedor_telefone = ?, vendedor_email = ?, vendedor_comissao_percentual = ?,
                vendedor_data_admissao = ?, vendedor_supervisor_id = ?, vendedor_observacoes = ?, vendedor_status = ?
                WHERE vendedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['matricula'] ?? null),
            !empty($dados['cpf']) ? preg_replace('/\D/', '', $dados['cpf']) : null, // Remove formatação
            trim($dados['endereco'] ?? null),
            trim($dados['bairro'] ?? null),
            trim($dados['cidade'] ?? null),
            !empty($dados['estado_id']) ? (int)$dados['estado_id'] : null,
            trim($dados['cep'] ?? null),
            trim($dados['telefone'] ?? null),
            trim($dados['email'] ?? null),
            (float)($dados['comissao_percentual'] ?? 0.00),
            !empty($dados['data_admissao']) ? date('Y-m-d', strtotime($dados['data_admissao'])) : null, // Formato YYYY-MM-DD
            !empty($dados['supervisor_id']) ? (int)$dados['supervisor_id'] : null,
            trim($dados['observacoes'] ?? null),
            $dados['status'] ?? 'ativo',
            $id
        ]);
    }

    /**
     * Exclui um vendedor.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Impede a exclusão do vendedor "Administrador" (ID 1) - Apenas exemplo, ajuste conforme necessário
        // if ($id == 1) {
        //     return false; // Ou lançar uma exceção
        // }

        // Verificar se está sendo usado por algum produto/pedido (opcional, mas boa prática)
        // $sqlCheck = "SELECT COUNT(*) FROM <tabela_relacionada> WHERE vendedor_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM vendedores WHERE vendedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CPF já existe (excluindo o vendedor com $idExcluir).
     *
     * @param string $cpf
     * @param int|null $idExcluir
     * @return bool
     */
    public function cpfExiste($cpf, $idExcluir = null) {
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        if (empty($cpfLimpo)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM vendedores WHERE vendedor_cpf = ? AND vendedor_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cpfLimpo, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM vendedores WHERE vendedor_cpf = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cpfLimpo]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o E-mail já existe (excluindo o vendedor com $idExcluir).
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
            $sql = "SELECT COUNT(*) FROM vendedores WHERE vendedor_email = ? AND vendedor_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM vendedores WHERE vendedor_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se a Matrícula já existe (excluindo o vendedor com $idExcluir).
     *
     * @param string $matricula
     * @param int|null $idExcluir
     * @return bool
     */
    public function matriculaExiste($matricula, $idExcluir = null) {
         if (empty($matricula)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM vendedores WHERE vendedor_matricula = ? AND vendedor_id != ?";
            $