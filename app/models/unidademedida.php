<?php
# ========================================
# Model UnidadeMedida
# Local: /app/models/unidademedida.php
# ========================================

class unidademedida {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todas as unidades de medida.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM unidademedidas ORDER BY unidademedida_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma unidade de medida pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM unidademedidas WHERE unidademedida_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria uma nova unidade de medida.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO unidademedidas
                (unidademedida_nome, unidademedida_sigla, unidademedida_descricao)
                VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['sigla']),
            trim($dados['descricao'] ?? '')
        ]);
    }

    /**
     * Atualiza uma unidade de medida existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE unidademedidas SET
                unidademedida_nome = ?,
                unidademedida_sigla = ?,
                unidademedida_descricao = ?
                WHERE unidademedida_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['sigla']),
            trim($dados['descricao'] ?? ''),
            $id
        ]);
    }

    /**
     * Exclui uma unidade de medida.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verificar se está sendo usada por algum produto
        $sqlCheck = "SELECT COUNT(*) FROM produtos WHERE produto_unidade_medida_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Não permite exclusão se estiver em uso
        }

        $sql = "DELETE FROM unidademedidas WHERE unidademedida_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o nome da unidade de medida já existe (excluindo a unidade com $idExcluir).
     *
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeExiste($nome, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM unidademedidas WHERE unidademedida_nome = ? AND unidademedida_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM unidademedidas WHERE unidademedida_nome = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se a sigla da unidade de medida já existe (excluindo a unidade com $idExcluir).
     *
     * @param string $sigla
     * @param int|null $idExcluir
     * @return bool
     */
    public function siglaExiste($sigla, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM unidademedidas WHERE unidademedida_sigla = ? AND unidademedida_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$sigla, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM unidademedidas WHERE unidademedida_sigla = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$sigla]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>