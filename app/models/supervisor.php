<?php
# ========================================
# Model Supervisor
# Local: /app/models/supervisor.php
# ========================================

class Supervisor {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os supervisores.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM supervisores ORDER BY supervisor_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um supervisor pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM supervisores WHERE supervisor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo supervisor.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO supervisores (supervisor_nome, supervisor_email, supervisor_telefone) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['email'] ?? null,
            $dados['telefone'] ?? null
        ]);
    }

    /**
     * Atualiza um supervisor existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE supervisores SET supervisor_nome = ?, supervisor_email = ?, supervisor_telefone = ? WHERE supervisor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $dados['email'] ?? null,
            $dados['telefone'] ?? null,
            $id
        ]);
    }

    /**
     * Exclui um supervisor.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usado por alguma unidade de saúde
        $sqlCheck = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_supervisor_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Não permite exclusão se estiver em uso
        }

        $sql = "DELETE FROM supervisores WHERE supervisor_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o Email já existe (excluindo o supervisor com $idExcluir).
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
            $sql = "SELECT COUNT(*) FROM supervisores WHERE supervisor_email = ? AND supervisor_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM supervisores WHERE supervisor_email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>