<?php
# ========================================
# Model Distrito
# Local: /app/models/distrito.php
# ========================================

class Distrito {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todos os distritos.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT * FROM distritos ORDER BY distrito_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um distrito pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM distritos WHERE distrito_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo distrito.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO distritos (distrito_nome) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome']
        ]);
    }

    /**
     * Atualiza um distrito existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        // Verifica se é o distrito "SEM DISTRITO" (ID 1) para evitar alteração do nome
        if ($id == 1) {
             // Não atualiza o nome, apenas retorna sucesso
             return true;
        }
        $sql = "UPDATE distritos SET distrito_nome = ? WHERE distrito_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome'],
            $id
        ]);
    }

    /**
     * Exclui um distrito.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Impede a exclusão do distrito "SEM DISTRITO" (ID 1)
        if ($id == 1) {
            return false; // Ou lançar uma exceção
        }

        // Verifica se está sendo usado por alguma unidade de saúde
        $sqlCheck = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_distrito_id = ?";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Não permite exclusão se estiver em uso
        }

        $sql = "DELETE FROM distritos WHERE distrito_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

     /**
     * Verifica se o nome do distrito já existe (excluindo o distrito com $idExcluir).
     *
     * @param string $nome
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeExiste($nome, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM distritos WHERE distrito_nome = ? AND distrito_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM distritos WHERE distrito_nome = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nome]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>