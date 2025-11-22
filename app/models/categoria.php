<?php
# ========================================
# Model Categoria
# Local: /app/models/categoria.php
# ========================================

class Categoria {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todas as categorias.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM categorias ORDER BY categoria_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma categoria pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM categorias WHERE categoria_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria uma nova categoria.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO categorias (categoria_nome, categoria_descricao) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'] ?? null
        ]);
    }

    /**
     * Atualiza uma categoria existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE categorias SET categoria_nome = ?, categoria_descricao = ? WHERE categoria_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'] ?? null,
            $id
        ]);
    }

    /**
     * Exclui uma categoria.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usada por algum produto (opcional, mas boa prática)
        // $sqlCheck = "SELECT COUNT(*) FROM produtos WHERE categoria_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     // Não permite exclusão se estiver em uso
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM categorias WHERE categoria_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o nome da categoria já existe (excluindo a categoria com $idExcluir).
     *
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeExiste($nome, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM categorias WHERE categoria_nome = ? AND categoria_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM categorias WHERE categoria_nome = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>