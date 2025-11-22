<?php
# ========================================
# Dashboard (Tela Principal)
# Local: /app/views/dashboard.php
# ========================================

session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../../app/views/login.php');
  exit;
}
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Sistema IGA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
  <!-- Top Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../../assets/img/img_logo.png" alt="Logo IGA" style="width: 140px;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <!-- Cadastro -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuCadastro" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-database"></i> Cadastro
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Produtos</a></li>
              <li><a class="dropdown-item" href="#">Categorias</a></li>
              <li><a class="dropdown-item" href="#">Unidades de Saúde</a></li>
              <li><a class="dropdown-item" href="#">Distritos</a></li>
              <li><a class="dropdown-item" href="#">Fornecedores</a></li>
              <li><a class="dropdown-item" href="#">Supervisores</a></li>
              <li><a class="dropdown-item" href="#">Fabricantes</a></li>
              <li><a class="dropdown-item" href="#">Transportadoras</a></li>
              <!-- Link para CRUD de usuários - SOMENTE PARA ADMINISTRADORES -->
              <?php if ($_SESSION['usuario_papel'] === 'Administrador'): ?>
              <li><a class="dropdown-item" href="../../app/controllers/usuario_controller.php?acao=listar">
                <i class="fas fa-users-cog text-success"></i> Usuários
              </a></li>
              <?php endif; ?>
            </ul>
          </li>

          <!-- Movimentação -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuMov" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-exchange-alt"></i> Movimentação
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Entrada de Produtos</a></li>
              <li><a class="dropdown-item" href="#">Requisições de Saída</a></li>
              <li><a class="dropdown-item" href="#">Acerto de Estoque</a></li>
            </ul>
          </li>

          <!-- Relatórios -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuRel" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-chart-bar"></i> Relatórios
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Produtos</a></li>
              <li><a class="dropdown-item" href="#">Unidades de Saúde</a></li>
              <li><a class="dropdown-item" href="#">Fornecedores</a></li>
              <li><a class="dropdown-item" href="#">Supervisores</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Requisições de Saída</a></li>
              <li><a class="dropdown-item" href="#">Inventário</a></li>
              <li><a class="dropdown-item" href="#">Kardex</a></li>
              <li><a class="dropdown-item" href="#">Custos</a></li>
              <li><a class="dropdown-item" href="#">Curva ABC</a></li>
            </ul>
          </li>

          <!-- Configurações -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuConfig" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-cogs"></i> Configurações
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Gerais</a></li>
              <li><a class="dropdown-item" href="#">Níveis de Usuário</a></li>
            </ul>
          </li>
        </ul>

        <ul class="navbar-nav">
          <li class="nav-item">
            <span class="nav-link text-dark">Olá, <?= htmlspecialchars($usuario_nome); ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="/iga/app/controllers/logout.php">Sair</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTEÚDO PRINCIPAL -->
  <div class="container mt-4">
    <h3 class="mb-4">Bem-vindo, <?= htmlspecialchars($usuario_nome); ?>!</h3>

    <!-- CARDS DE INDICADORES -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #0b9960;">
          <div class="card-body">
            <h5 class="card-title">Produtos</h5>
            <p class="card-text fs-4">120</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #6cd4ac;">
          <div class="card-body">
            <h5 class="card-title">Estoque Total</h5>
            <p class="card-text fs-4">1.985</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #073725;">
          <div class="card-body">
            <h5 class="card-title">Entradas (mês)</h5>
            <p class="card-text fs-4">43</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #cbd9d3;">
          <div class="card-body">
            <h5 class="card-title text-dark">Saídas (mês)</h5>
            <p class="card-text text-dark fs-4">28</p>
          </div>
        </div>
      </div>
    </div>

    <!-- TABELA DE PRODUTOS E ESTOQUE ATUAL -->
    <div class="card">
      <div class="card-header" style="background-color: #0b9960; color: white;">
        Produtos em Estoque
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Apresentação</th>
                <th>Marca</th>
                <th>Estoque Atual</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Dipirona Sódica 500mg</td>
                <td>Medicamento</td>
                <td>Comprimido</td>
                <td>Neo Química</td>
                <td>240</td>
              </tr>
              <tr>
                <td>Soro Fisiológico 0,9%</td>
                <td>Injetáveis</td>
                <td>Frasco 500ml</td>
                <td>JP Farma</td>
                <td>320</td>
              </tr>
              <tr>
                <td>Paracetamol 750mg</td>
                <td>Medicamento</td>
                <td>Comprimido</td>
                <td>EMS</td>
                <td>180</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center mt-5 py-4 border-top">
    <strong>&copy; <?= date('Y'); ?> Sistema de Controle de Estoque IGA</strong>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>