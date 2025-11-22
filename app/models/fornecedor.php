<?php
# ========================================
# Model Fornecedor
# Local: /app/models/Fornecedor.php
# ========================================

class Fornecedor {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os fornecedores com o nome do estado.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT f.*, e.estado_nome, e.estado_uf 
                FROM fornecedores f 
                LEFT JOIN estados e ON f.fornecedor_estado_id = e.estado_id 
                ORDER BY f.fornecedor_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um fornecedor pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM fornecedores WHERE fornecedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo fornecedor.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO fornecedores 
                (fornecedor_nome, fornecedor_razaosocial, fornecedor_cnpj, 
                 fornecedor_endereco, fornecedor_bairro, fornecedor_cidade, 
                 fornecedor_estado_id, fornecedor_telefone, fornecedor_email) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['razaosocial'] ?? null,
            !empty($dados['cnpj']) ? preg_replace('/\D/', '', $dados['cnpj']) : null, // Remove formatação
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado_id'] ?? null,
            $dados['telefone'] ?? null,
            $dados['email'] ?? null
        ]);
    }

    /**
     * Atualiza um fornecedor existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE fornecedores SET 
                fornecedor_nome = ?, fornecedor_razaosocial = ?, fornecedor_cnpj = ?, 
                fornecedor_endereco = ?, fornecedor_bairro = ?, fornecedor_cidade = ?, 
                fornecedor_estado_id = ?, fornecedor_telefone = ?, fornecedor_email = ? 
                WHERE fornecedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['razaosocial'] ?? null,
            !empty($dados['cnpj']) ? preg_replace('/\D/', '', $dados['cnpj']) : null, // Remove formatação
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado_id'] ?? null,
            $dados['telefone'] ?? null,
            $dados['email'] ?? null,
            $id
        ]);
    }

    /**
     * Exclui um fornecedor.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usado por algum produto (opcional, mas boa prática)
        // $sqlCheck = "SELECT COUNT(*) FROM produtos WHERE fornecedor_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     // Não permite exclusão se estiver em uso
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM fornecedores WHERE fornecedor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CNPJ já existe (excluindo o fornecedor com $idExcluir).
     *
     * @param string $cnpj
     * @param int|null $idExcluir
     * @return bool
     */
    public function cnpjExiste($cnpj, $idExcluir = null) {
        $cnpjLimpo = preg_replace('/\D/', '', $cnpj);
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM fornecedores WHERE fornecedor_cnpj = ? AND fornecedor_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjLimpo, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM fornecedores WHERE fornecedor_cnpj = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjLimpo]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o Email já existe (excluindo o fornecedor com $idExcluir).
     *
     * @param string $email
     * @param int|null $idExcluir
     * @return bool
     */
    public function emailExiste($email, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM fornecedores WHERE fornecedor_email = ? AND fornecedor_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM fornecedores WHERE fornecedor_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }


    /**
     * Busca todos os estados para o dropdown.
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