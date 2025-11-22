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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Sistema 3M3 Partners</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
  <!-- Top Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../../assets/img/img_logo.png" alt="Logo 3M3 Partners" style="width: 140px;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <!-- Cadastros (Agora no plural) -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuCadastros" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-database"></i> Cadastros
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../../app/controllers/produto_controller.php?acao=listar"><i class="fas fa-box-open"></i> Produtos</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/categoria_controller.php?acao=listar"><i class="fas fa-tags"></i> Categorias</a></li>
              <!-- === ALTERAÇÃO: Substituir "Unidades de Saúde" por "Clientes" === -->
              <li><a class="dropdown-item" href="../../app/controllers/cliente_controller.php?acao=listar"><i class="fas fa-hospital"></i> Clientes</a></li>
              <!-- ============================================================= -->
              <li><a class="dropdown-item" href="../../app/controllers/distrito_controller.php?acao=listar"><i class="fas fa-map-marker-alt"></i> Distritos</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/fornecedor_controller.php?acao=listar"><i class="fas fa-truck"></i> Fornecedores</a></li>
              <!-- === ALTERAÇÃO: Substituir "Supervisores" por "Vendedores" === -->
              <li><a class="dropdown-item" href="../../app/controllers/vendedor_controller.php?acao=listar"><i class="fas fa-user-tie"></i> Vendedores</a></li>
              <!-- ========================================================== -->
              <li><a class="dropdown-item" href="../../app/controllers/fabricante_controller.php?acao=listar"><i class="fas fa-industry"></i> Fabricantes</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/transportadora_controller.php?acao=listar"><i class="fas fa-shipping-fast"></i> Transportadoras</a></li>
              <!-- === ADIÇÃO: Link para Unidades de Medida === -->
              <li><a class="dropdown-item" href="../../app/controllers/unidademedida_controller.php?acao=listar"><i class="fas fa-ruler-combined"></i> Unidades de Medida</a></li>
              <!-- ========================================== -->
              <!-- Link para CRUD de usuários - SEM ÍCONE, SOMENTE PARA ADMINISTRADORES -->
              <?php if ($_SESSION['usuario_papel'] === 'Administrador'): ?>
              <li><a class="dropdown-item" href="../../app/controllers/usuario_controller.php?acao=listar">
                <!-- ÍCONE REMOVIDO CONFORME SOLICITADO -->
                Usuários
              </a></li>
              <?php endif; ?>
            </ul>
          </li>

          <!-- Movimentações (Agora no plural) -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuMov" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-exchange-alt"></i> Movimentações
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../../app/controllers/entrada_controller.php?acao=listar"><i class="fas fa-sign-in-alt"></i> Entrada de Produtos</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/requisicao_controller.php?acao=listar"><i class="fas fa-sign-out-alt"></i> Requisições de Saída</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/ajuste_controller.php?acao=listar"><i class="fas fa-balance-scale"></i> Acerto de Estoque</a></li>
            </ul>
          </li>

          <!-- Relatórios -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuRel" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-chart-bar"></i> Relatórios
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=produtos"><i class="fas fa-box"></i> Produtos</a></li>
              <!-- === ALTERAÇÃO: Substituir "Unidades de Saúde" por "Clientes" === -->
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=clientes"><i class="fas fa-clinic-medical"></i> Clientes</a></li>
              <!-- ============================================================= -->
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=fornecedores"><i class="fas fa-truck-loading"></i> Fornecedores</a></li>
              <!-- === ALTERAÇÃO: Substituir "Supervisores" por "Vendedores" === -->
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=vendedores"><i class="fas fa-user-tie"></i> Vendedores</a></li>
              <!-- ========================================================== -->
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=requisicoes"><i class="fas fa-file-export"></i> Requisições de Saída</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=inventario"><i class="fas fa-clipboard-list"></i> Inventário</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=kardex"><i class="fas fa-receipt"></i> Kardex</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=custos"><i class="fas fa-money-bill-wave"></i> Custos</a></li>
              <li><a class="dropdown-item" href="../../app/controllers/relatorio_controller.php?tipo=curva-abc"><i class="fas fa-chart-line"></i> Curva ABC</a></li>
            </ul>
          </li>

          <!-- Configurações -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="menuConfig" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-cogs"></i> Configurações
            </a>
            <ul class="dropdown-menu">
              <!-- === ALTERAÇÃO: Link para Configurações Gerais === -->
              <li><a class="dropdown-item" href="../../app/controllers/configuracoesgerais_controller.php?acao=editar"><i class="fas fa-building"></i> Gerais</a></li>
              <!-- ================================================= -->
              <!-- === ALTERAÇÃO: Link para Níveis de Usuário === -->
              <?php if ($_SESSION['usuario_papel'] === 'Administrador'): ?>
              <li><a class="dropdown-item" href="../../app/controllers/usuariopapel_controller.php?acao=listar">
                 <i class="fas fa-user-tag"></i> Níveis de Usuário
              </a></li>
              <?php endif; ?>
              <!-- ============================================= -->
            </ul>
          </li>
        </ul>

        <ul class="navbar-nav">
          <li class="nav-item">
            <span class="nav-link text-dark">Olá, <?= htmlspecialchars($usuario_nome); ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="../../app/controllers/logout.php">Sair</a>
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
        <div class="card text-white" style="background-color: #0073EC;"> <!-- Azul Vibrante -->
          <div class="card-body">
            <h5 class="card-title">Produtos</h5>
            <p class="card-text fs-4">120</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #051326;"> <!-- Azul Marinho Escuro -->
          <div class="card-body">
            <h5 class="card-title">Estoque Total</h5>
            <p class="card-text fs-4">1.985</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #020A17;"> <!-- Preto Quase Puro -->
          <div class="card-body">
            <h5 class="card-title">Entradas (mês)</h5>
            <p class="card-text fs-4">43</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white" style="background-color: #cbd9d3;"> <!-- Cinza Claro -->
          <div class="card-body">
            <h5 class="card-title text-dark">Saídas (mês)</h5>
            <p class="card-text text-dark fs-4">28</p>
          </div>
        </div>
      </div>
    </div>

    <!-- TABELA DE PRODUTOS E ESTOQUE ATUAL -->
    <div class="card">
      <div class="card-header" style="background-color: #0073EC; color: white;"> <!-- Azul Vibrante -->
        Produtos em Estoque
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0">
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
    <strong>&copy; <?= date('Y'); ?> | 3M3 Partners - Todos Direitos Reservados</strong>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>