<?php
# ========================================
# Model UsuarioPapel
# Local: /app/models/usuariopapel.php
# ========================================

class usuariopapel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os papéis de usuário.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT up.*, COUNT(u.usuario_id) as qtd_usuarios
                FROM usuariopapeis up
                LEFT JOIN usuarios u ON up.usuariopapel_id = u.papel_id
                GROUP BY up.usuariopapel_id, up.usuariopapel_nome, up.usuariopapel_descricao, up.usuariopapel_criadoem
                ORDER BY up.usuariopapel_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um papel de usuário pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM usuariopapeis WHERE usuariopapel_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo papel de usuário.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO usuariopapeis (usuariopapel_nome, usuariopapel_descricao) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['descricao'] ?? '')
        ]);
    }

    /**
     * Atualiza um papel de usuário existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        // Verifica se é o papel 'Administrador' (ID 1) para evitar alteração do nome
        if ($id == 1) {
             // Não atualiza o nome, apenas a descrição
             $sql = "UPDATE usuariopapeis SET usuariopapel_descricao = ? WHERE usuariopapel_id = ?";
             $stmt = $this->pdo->prepare($sql);
             return $stmt->execute([
                 trim($dados['descricao'] ?? ''),
                 $id
             ]);
        }

        $sql = "UPDATE usuariopapeis SET usuariopapel_nome = ?, usuariopapel_descricao = ? WHERE usuariopapel_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            trim($dados['nome']),
            trim($dados['descricao'] ?? ''),
            $id
        ]);
    }

    /**
     * Exclui um papel de usuário.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Impede a exclusão do papel 'Administrador' (ID 1)
        if ($id == 1) {
            return false; // Ou lançar uma exceção
        }

        // Verifica se está sendo usado por algum usuário
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE papel_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Não permite exclusão se estiver em uso
        }

        $sql = "DELETE FROM usuariopapeis WHERE usuariopapel_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o nome do papel já existe (excluindo o papel com $idExcluir).
     *
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeExiste($nome, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM usuariopapeis WHERE usuariopapel_nome = ? AND usuariopapel_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM usuariopapeis WHERE usuariopapel_nome = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>