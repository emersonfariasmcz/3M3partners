<?php
# ========================================
# Model ConfiguracoesGerais
# Local: /app/models/configuracoesgerais.php
# ========================================

class configuracoesgerais {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Busca as configurações gerais com o nome do estado.
     *
     * @return array|false
     */
    public function buscarConfiguracoes() {
        // LEFT JOIN para garantir que retorne o registro mesmo que estado_id seja NULL
        $sql = "SELECT cg.*, e.estado_nome AS config_estado_nome, e.estado_uf AS config_estado_uf
                FROM configuracoesgerais cg
                LEFT JOIN estados e ON cg.config_estado_id = e.estado_id
                ORDER BY cg.config_id ASC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    /**
     * Atualiza as configurações gerais.
     *
     * @param array $dados
     * @return bool
     */
    public function atualizar($dados) {
        // Verifica se já existe um registro
        $configuracaoAtual = $this->buscarConfiguracoes();

        // Processa o estado_id
        $estado_id = !empty($dados['estado_id']) ? (int)$dados['estado_id'] : null;

        if ($configuracaoAtual) {
            // Atualiza o registro existente
            $sql = "UPDATE configuracoesgerais SET
                    config_nome_empresa = ?,
                    config_cnpj = ?,
                    config_endereco = ?,
                    config_cidade = ?,
                    config_estado_id = ?, -- Atualiza o ID do estado
                    config_cep = ?,
                    config_telefone = ?,
                    config_email = ?,
                    config_site = ?,
                    config_logo_path = ?
                    WHERE config_id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                trim($dados['nome_empresa']),
                !empty($dados['cnpj']) ? trim($dados['cnpj']) : null,
                trim($dados['endereco']),
                trim($dados['cidade']),
                $estado_id,
                !empty($dados['cep']) ? trim($dados['cep']) : null,
                !empty($dados['telefone']) ? trim($dados['telefone']) : null,
                !empty($dados['email']) ? trim($dados['email']) : null,
                !empty($dados['site']) ? trim($dados['site']) : null,
                !empty($dados['logo_path']) ? trim($dados['logo_path']) : 'assets/img/img_logo.png',
                $configuracaoAtual['config_id']
            ]);
        } else {
            // Cria um novo registro (caso não exista)
            $sql = "INSERT INTO configuracoesgerais (
                        config_nome_empresa,
                        config_cnpj,
                        config_endereco,
                        config_cidade,
                        config_estado_id,
                        config_cep,
                        config_telefone,
                        config_email,
                        config_site,
                        config_logo_path
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                trim($dados['nome_empresa']),
                !empty($dados['cnpj']) ? trim($dados['cnpj']) : null,
                trim($dados['endereco']),
                trim($dados['cidade']),
                $estado_id,
                !empty($dados['cep']) ? trim($dados['cep']) : null,
                !empty($dados['telefone']) ? trim($dados['telefone']) : null,
                !empty($dados['email']) ? trim($dados['email']) : null,
                !empty($dados['site']) ? trim($dados['site']) : null,
                !empty($dados['logo_path']) ? trim($dados['logo_path']) : 'assets/img/img_logo.png'
            ]);
        }
    }
}
?>