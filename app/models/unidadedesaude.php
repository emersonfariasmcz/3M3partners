<?php
# ========================================
# Model Unidade de Saúde
# Local: /app/models/unidadedesaude.php
# ========================================

class UnidadeDeSaude {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lista todas as unidades de saúde com dados relacionados.
     *
     * @return array
     */
    public function listarTodos() {
        $sql = "SELECT u.*, e.estado_nome, e.estado_uf, d.distrito_nome, s.supervisor_nome
                FROM unidadesdesaude u
                LEFT JOIN estados e ON u.unidadedesaude_estado_id = e.estado_id
                LEFT JOIN distritos d ON u.unidadedesaude_distrito_id = d.distrito_id
                LEFT JOIN supervisores s ON u.unidadedesaude_supervisor_id = s.supervisor_id
                ORDER BY u.unidadedesaude_nomeabrev ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma unidade de saúde pelo ID.
     *
     * @param int $id
     * @return array|false
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM unidadesdesaude WHERE unidadedesaude_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria uma nova unidade de saúde.
     *
     * @param array $dados
     * @return bool
     */
    public function criar($dados) {
        $sql = "INSERT INTO unidadesdesaude 
                (unidadedesaude_nomecomp, unidadedesaude_nomeabrev, unidadedesaude_endereco,
                 unidadedesaude_bairro, unidadedesaude_cidade, unidadedesaude_estado_id,
                 unidadedesaude_direcaoadm, unidadedesaude_distrito_id, unidadedesaude_supervisor_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome_completo'],
            $dados['nome_abreviado'],
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado_id'] ?? null,
            $dados['direcao_adm'] ?? null,
            $dados['distrito_id'],
            $dados['supervisor_id']
        ]);
    }

    /**
     * Atualiza uma unidade de saúde existente.
     *
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        $sql = "UPDATE unidadesdesaude SET 
                unidadedesaude_nomecomp = ?, unidadedesaude_nomeabrev = ?, unidadedesaude_endereco = ?,
                unidadedesaude_bairro = ?, unidadedesaude_cidade = ?, unidadedesaude_estado_id = ?,
                unidadedesaude_direcaoadm = ?, unidadedesaude_distrito_id = ?, unidadedesaude_supervisor_id = ?
                WHERE unidadedesaude_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $dados['nome_completo'],
            $dados['nome_abreviado'],
            $dados['endereco'] ?? null,
            $dados['bairro'] ?? null,
            $dados['cidade'] ?? null,
            $dados['estado_id'] ?? null,
            $dados['direcao_adm'] ?? null,
            $dados['distrito_id'],
            $dados['supervisor_id'],
            $id
        ]);
    }

    /**
     * Exclui uma unidade de saúde.
     *
     * @param int $id
     * @return bool
     */
    public function excluir($id) {
        // Verifica se está sendo usada em outra parte do sistema (opcional, mas boa prática)
        // $sqlCheck = "SELECT COUNT(*) FROM <tabela_relacionada> WHERE unidadedesaude_id = ?";
        // $stmtCheck = $this->pdo->prepare($sqlCheck);
        // $stmtCheck->execute([$id]);
        // if ($stmtCheck->fetchColumn() > 0) {
        //     return false; // Ou lançar uma exceção
        // }

        $sql = "DELETE FROM unidadesdesaude WHERE unidadedesaude_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifica se o nome completo já existe (excluindo a unidade com $idExcluir).
     *
     * @param string $nomeCompleto
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeCompletoExiste($nomeCompleto, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_nomecomp = ? AND unidadedesaude_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nomeCompleto, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_nomecomp = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nomeCompleto]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se o nome abreviado já existe (excluindo a unidade com $idExcluir).
     *
     * @param string $nomeAbreviado
     * @param int|null $idExcluir
     * @return bool
     */
    public function nomeAbreviadoExiste($nomeAbreviado, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_nomeabrev = ? AND unidadedesaude_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nomeAbreviado, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) FROM unidadesdesaude WHERE unidadedesaude_nomeabrev = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nomeAbreviado]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // Métodos auxiliares para dropdowns

    /**
     * Busca todos os estados.
     *
     * @return array
     */
    public function listarEstados() {
        $sql = "SELECT estado_id, estado_nome, estado_uf FROM estados ORDER BY estado_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os distritos.
     *
     * @return array
     */
    public function listarDistritos() {
        $sql = "SELECT distrito_id, distrito_nome FROM distritos ORDER BY distrito_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os supervisores.
     *
     * @return array
     */
    public function listarSupervisores() {
        $sql = "SELECT supervisor_id, supervisor_nome FROM supervisores ORDER BY supervisor_nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>