<?php
# ========================================
# Model Transportadora
# Local: /app/models/transportadora.php
# ========================================

class Transportadora {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todas as transportadoras.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM transportadoras ORDER BY transportadora_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma transportadora pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM transportadoras WHERE transportadora_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria uma nova transportadora.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO transportadoras 
                (transportadora_nome, transportadora_cnpj, transportadora_endereco, transportadora_bairro,
                 transportadora_cidade, transportadora_estado, transportadora_telefone, transportadora_email) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            !empty($dados['cnpj']) ? (int)preg_replace('/\D/', '', $dados['cnpj']) : null, // Converte para INT conforme definição da tabela
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            !empty($dados['telefone']) ? (int)preg_replace('/\D/', '', $dados['telefone']) : null, // Converte para INT
            $dados['email'] ?? null
        ]);
    }

    /**
     * Atualiza uma transportadora existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE transportadoras SET 
                transportadora_nome = ?, transportadora_cnpj = ?, transportadora_endereco = ?, transportadora_bairro = ?,
                transportadora_cidade = ?, transportadora_estado = ?, transportadora_telefone = ?, transportadora_email = ?
                WHERE transportadora_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            !empty($dados['cnpj']) ? (int)preg_replace('/\D/', '', $dados['cnpj']) : null, // Converte para INT
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado'] ?? null,
            !empty($dados['telefone']) ? (int)preg_replace('/\D/', '', $dados['telefone']) : null, // Converte para INT
            $dados['email'] ?? null,
            $id
        ]);
    }

    /**
     * Exclui uma transportadora.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usada em outra parte do sistema (opcional)
        // $sqlCheck = "SELECT COUNT(*) FROM <tabela_relacionada> WHERE transportadora_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM transportadoras WHERE transportadora_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o CNPJ já existe (excluindo a transportadora com $idExcluir).
     *
     * @param string $cnpj
     * @param int|null $idExcluir
     * @return bool
     */
    public function cnpjExiste($cnpj, $idExcluir = null) {
        $cnpjInt = !empty($cnpj) ? (int)preg_replace('/\D/', '', $cnpj) : null;
        if (empty($cnpjInt)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM transportadoras WHERE transportadora_cnpj = ? AND transportadora_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjInt, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM transportadoras WHERE transportadora_cnpj = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cnpjInt]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o Email já existe (excluindo a transportadora com $idExcluir).
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
            $sql = "SELECT COUNT(*) FROM transportadoras WHERE transportadora_email = ? AND transportadora_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM transportadoras WHERE transportadora_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>