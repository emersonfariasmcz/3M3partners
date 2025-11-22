<?php
# ========================================
# Model Fabricante
# Local: /app/models/fabricante.php
# ========================================

class Fabricante {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os fabricantes.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM fabricantes ORDER BY fabricante_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um fabricante pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM fabricantes WHERE fabricante_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo fabricante.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO fabricantes 
                (fabricante_nome, fabricante_cnpj, fabricante_telefone, fabricante_email,
                 fabricante_endereco, fabricante_cidade, fabricante_estado, fabricante_cep,
                 fabricante_observacoes, fabricante_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            !empty($dados['cnpj']) ? preg_replace('/\D/', '', $dados['cnpj']) : null, // Remove formatação
            $dados['telefone'] ?? null,
            $dados['email'] ?? null,
            $dados['endereco'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            $dados['cep'] ?? null,
            $dados['observacoes'] ?? null,
            $dados['status'] ?? 'ativo'
        ]);
    }

    /**
     * Atualiza um fabricante existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE fabricantes SET 
                fabricante_nome = ?, fabricante_cnpj = ?, fabricante_telefone = ?, fabricante_email = ?,
                fabricante_endereco = ?, fabricante_cidade = ?, fabricante_estado = ?, fabricante_cep = ?,
                fabricante_observacoes = ?, fabricante_status = ?, fabricante_atualizadoem = CURRENT_TIMESTAMP
                WHERE fabricante_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            !empty($dados['cnpj']) ? preg_replace('/\D/', '', $dados['cnpj']) : null, // Remove formatação
            $dados['telefone'] ?? null,
            $dados['email'] ?? null,
            $dados['endereco'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            $dados['cep'] ?? null,
            $dados['observacoes'] ?? null,
            $dados['status'] ?? 'ativo',
            $id
        ]);
    }

    /**
     * Exclui um fabricante.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usado por algum produto (opcional)
        // $sqlCheck = "SELECT COUNT(*) FROM produtos WHERE fabricante_id = ?"; // Ajuste conforme sua tabela produtos
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM fabricantes WHERE fabricante_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CNPJ já existe (excluindo o fabricante com $idExcluir).
     *
     * @param string $cnpj
     * @param int|null $idExcluir
     * @return bool
     */
    public function cnpjExiste($cnpj, $idExcluir = null) {
        $cnpjLimpo = preg_replace('/\D/', '', $cnpj);
        if (empty($cnpjLimpo)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM fabricantes WHERE fabricante_cnpj = ? AND fabricante_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjLimpo, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM fabricantes WHERE fabricante_cnpj = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjLimpo]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o Email já existe (excluindo o fabricante com $idExcluir).
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
            $sql = "SELECT COUNT(*) FROM fabricantes WHERE fabricante_email = ? AND fabricante_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM fabricantes WHERE fabricante_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>