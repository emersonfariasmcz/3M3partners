<?php
# ========================================
# Model Produto
# Local: /app/models/produto.php
# ========================================

class Produto {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os produtos com informações das tabelas relacionadas.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT p.*, c.categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
                ORDER BY p.produto_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um produto pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM produtos WHERE produto_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo produto.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO produtos 
                (produto_nome, produto_descricao, produto_codigo_barras, produto_unidade_medida,
                 produto_estoque_minimo, produto_estoque_maximo, produto_preco_custo,
                 produto_preco_venda, categoria_id, produto_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'] ?? null,
            $dados['codigo_barras'] ?? null,
            $dados['unidade_medida'] ?? 'UN',
            $dados['estoque_minimo'] ?? 0,
            $dados['estoque_maximo'] ?? 0,
            $dados['preco_custo'] ?? 0.00,
            $dados['preco_venda'] ?? 0.00,
            $dados['categoria_id'],
            $dados['status'] ?? 'ativo'
        ]);
    }

    /**
     * Atualiza um produto existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE produtos SET 
                produto_nome = ?, produto_descricao = ?, produto_codigo_barras = ?, 
                produto_unidade_medida = ?, produto_estoque_minimo = ?, produto_estoque_maximo = ?, 
                produto_preco_custo = ?, produto_preco_venda = ?, categoria_id = ?, 
                produto_status = ?
                WHERE produto_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['descricao'] ?? null,
            $dados['codigo_barras'] ?? null,
            $dados['unidade_medida'] ?? 'UN',
            $dados['estoque_minimo'] ?? 0,
            $dados['estoque_maximo'] ?? 0,
            $dados['preco_custo'] ?? 0.00,
            $dados['preco_venda'] ?? 0.00,
            $dados['categoria_id'],
            $dados['status'] ?? 'ativo',
            $id
        ]);
    }

    /**
     * Exclui um produto.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        $sql = "DELETE FROM produtos WHERE produto_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o nome do produto já existe (excluindo o produto com $idExcluir).
     *
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeExiste($nome, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM produtos WHERE produto_nome = ? AND produto_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM produtos WHERE produto_nome = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o código de barras já existe (excluindo o produto com $idExcluir).
     *
     * @param string $codigo_barras
     * @param int|null $idExcluir
     * @return bool
     */
    public function codigoBarrasExiste($codigo_barras, $idExcluir = null) {
        if (empty($codigo_barras)) {
            return false;
        }
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM produtos WHERE produto_codigo_barras = ? AND produto_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigo_barras, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM produtos WHERE produto_codigo_barras = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigo_barras]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // Métodos auxiliares para dropdowns

    /**
     * Busca todas as categorias ativas.
     *
     * @return array
     */
    public function listarCategoriasAtivas() {
        $sql = "SELECT categoria_id, categoria_nome FROM categorias WHERE categoria_status = 'ativo' ORDER BY categoria_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>