<?php
# ========================================
# Formulário de Criação de Produto
# Local: /app/views/produtos/create.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/controllers/acesso_negado.php');
    exit;
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

$sucesso = $_SESSION['sucesso_salvar'] ?? '';
unset($_SESSION['sucesso_salvar']);

if (!isset($categorias)) {
    header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto - Sistema IGA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-box me-2"></i>Novo Produto
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($sucesso): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($sucesso) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/produto_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Nome do Produto -->
                                <div class="col-md-8">
                                    <label for="produto_nome" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control" id="produto_nome" name="produto_nome" 
                                           required maxlength="100">
                                    <div class="invalid-feedback">
                                        Por favor, informe o nome do produto.
                                    </div>
                                </div>
                                
                                <!-- Categoria -->
                                <div class="col-md-4">
                                    <label for="categoria_id" class="form-label">Categoria *</label>
                                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                                        <option value="">Selecione uma categoria...</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= htmlspecialchars($categoria['categoria_id']) ?>">
                                                <?= htmlspecialchars($categoria['categoria_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecione uma categoria.
                                    </div>
                                </div>
                                
                                <!-- Descrição -->
                                <div class="col-md-12">
                                    <label for="produto_descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="produto_descricao" name="produto_descricao" 
                                              rows="3" maxlength="500"></textarea>
                                    <div class="form-text">Opcional - máximo 500 caracteres</div>
                                </div>
                                
                                <!-- Código de Barras -->
                                <div class="col-md-6">
                                    <label for="produto_codigo_barras" class="form-label">Código de Barras</label>
                                    <input type="text" class="form-control" id="produto_codigo_barras" name="produto_codigo_barras" 
                                           maxlength="50" placeholder="Opcional">
                                </div>
                                
                                <!-- Unidade de Medida -->
                                <div class="col-md-6">
                                    <label for="produto_unidade_medida" class="form-label">Unidade de Medida *</label>
                                    <input type="text" class="form-control" id="produto_unidade_medida" name="produto_unidade_medida" 
                                           required maxlength="20" placeholder="Ex: UN, CX, KG, etc.">
                                    <div class="invalid-feedback">
                                        Por favor, informe a unidade de medida.
                                    </div>
                                </div>
                                
                                <!-- Estoque Mínimo -->
                                <div class="col-md-4">
                                    <label for="produto_estoque_minimo" class="form-label">Estoque Mínimo</label>
                                    <input type="number" class="form-control" id="produto_estoque_minimo" name="produto_estoque_minimo" 
                                           min="0" value="0">
                                </div>
                                
                                <!-- Estoque Máximo -->
                                <div class="col-md-4">
                                    <label for="produto_estoque_maximo" class="form-label">Estoque Máximo</label>
                                    <input type="number" class="form-control" id="produto_estoque_maximo" name="produto_estoque_maximo" 
                                           min="0" value="0">
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-4">
                                    <label for="produto_status" class="form-label">Status *</label>
                                    <select class="form-select" id="produto_status" name="produto_status" required>
                                        <option value="ativo" selected>Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                
                                <!-- Preço de Custo -->
                                <div class="col-md-6">
                                    <label for="produto_preco_custo" class="form-label">Preço de Custo (R$)</label>
                                    <input type="number" class="form-control" id="produto_preco_custo" name="produto_preco_custo" 
                                           step="0.01" min="0" value="0.00">
                                </div>
                                
                                <!-- Preço de Venda -->
                                <div class="col-md-6">
                                    <label for="produto_preco_venda" class="form-label">Preço de Venda (R$)</label>
                                    <input type="number" class="form-control" id="produto_preco_venda" name="produto_preco_venda" 
                                           step="0.01" min="0" value="0.00">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/produto_controller.php?acao=listar" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validação do formulário
        (function () {
            'use strict'
            
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>