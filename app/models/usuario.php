<?php
# ========================================
# Model de Usuário
# Local: /app/models/Usuario.php
# ========================================

class Usuario
{
    private $pdo;

    // Construtor recebe a conexão com o banco de dados
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lista todos os usuários com nome do papel
    public function listarTodos()
    {
        $sql = "SELECT u.*, p.usuariopapel_nome
                FROM usuarios u
                JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                ORDER BY u.usuario_nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Busca um usuário específico pelo ID
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // Insere um novo usuário
    public function inserir($dados)
    {
        $sql = "INSERT INTO usuarios (usuario_nome, usuario_email, usuario_login, usuario_senha, papel_id)
                VALUES (:nome, :email, :login, :senha, :papel_id)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nome'     => $dados['usuario_nome'],
            ':email'    => $dados['usuario_email'],
            ':login'    => strtolower($dados['usuario_login']), // login sempre em minúsculas
            ':senha'    => password_hash($dados['usuario_senha'], PASSWORD_DEFAULT),
            ':papel_id' => $dados['papel_id']
        ]);
    }

    // Atualiza os dados de um usuário existente
    public function atualizar($id, $dados)
    {
        $sql = "UPDATE usuarios
                SET usuario_nome = :nome,
                    usuario_email = :email,
                    usuario_login = :login,
                    papel_id = :papel_id";

        // Se a senha for informada, atualiza também
        if (!empty($dados['usuario_senha'])) {
            $sql .= ", usuario_senha = :senha";
        }

        $sql .= " WHERE usuario_id = :id";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':nome'     => $dados['usuario_nome'],
            ':email'    => $dados['usuario_email'],
            ':login'    => strtolower($dados['usuario_login']),
            ':papel_id' => $dados['papel_id'],
            ':id'       => $id
        ];

        if (!empty($dados['usuario_senha'])) {
            $params[':senha'] = password_hash($dados['usuario_senha'], PASSWORD_DEFAULT);
        }

        return $stmt->execute($params);
    }

    // Exclui um usuário
    public function excluir($id)
    {
        $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Lista os papéis disponíveis (níveis de acesso)
    public function listarPapeis()
    {
        $sql = "SELECT usuariopapel_id, usuariopapel_nome FROM usuariopapeis ORDER BY usuariopapel_nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
