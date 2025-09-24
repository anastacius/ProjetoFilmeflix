<?php
//  Configurações da API
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$genero_id = 35;
$idioma = 'pt-BR';
$ordenacao = isset($_GET['sort']) ? $_GET['sort'] : 'title.asc';
$modo = isset($_GET['modo']) ? $_GET['modo'] : 'serio';
 
$url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&with_genres={$genero_id}&page={$pagina_atual}&language={$idioma}&sort_by={$ordenacao}&vote_count.gte=1000";
 
$json_string = @file_get_contents($url);
if ($json_string === false) {
  die('<div class="alert alert-danger text-center" role="alert">Erro ao se conectar com a API do TMDB.</div>');
}
$filmes_data = json_decode($json_string);
if (!$filmes_data || !isset($filmes_data->results)) {
  die('<div class="alert alert-warning text-center" role="alert">Nenhum filme encontrado.</div>');
}
 
$filmes = $filmes_data->results;
$total_paginas = $filmes_data->total_pages;
$base_image_url = 'https://image.tmdb.org/t/p/w500';
$busca = isset($_GET['search']) ? strtolower($_GET['search']) : '';
?>
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filmes de Comédia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./styles/style.css">
  <style>
    /*  Estilo extra para modo zoeiro */
    body.zoeiro {
      background: #060503ff;
    }
    .zoeiro .movie-item {
      transform: rotate(-1deg);
      transition: 0.3s;
    }
    .zoeiro .movie-item:hover {
      transform: rotate(2deg) scale(1.05);
    }
    .zoeiro h1::after {
      content: " ";
    }
    .zoeiro .page-link {
      background-color: #272005ff;
      border: 1px solid #312506ff;
    }
    .zoeiro .page-link:hover {
      background-color: #130e02ff;
      color: #463232ff;
    }
    .row-compact {
  margin-left: -10px;
  margin-right: -13px;
}

.row-compact > [class*="col-"] {
  padding-left: 11px;
  padding-right: 10px;
}

.movie-item {
  margin-bottom: 5px !important;
}

.card.p-2 {
  padding: 2px !important;
}
  </style>
</head>
<body class="<?php echo $modo === 'zoeiro' ? 'zoeiro' : ''; ?>">
 
<div class="container mt-5">
  <!-- Cabeçalho -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>🎭 Filmes de Comédia</h1>
    <!-- Botão modo -->
    <a href="?modo=<?php echo $modo === 'serio' ? 'zoeiro' : 'serio'; ?>&page=<?php echo $pagina_atual; ?>&sort=<?php echo $ordenacao; ?>&search=<?php echo $busca; ?>" 
       class="btn btn-<?php echo $modo === 'serio' ? 'warning' : 'secondary'; ?>">
       Modo <?php echo $modo === 'serio' ? 'ZOEIRO' : 'SÉRIO'; ?>
    </a>
  </div>
 
  <!-- Barra de busca -->
  <div class="row mb-4">
    <div class="col-md-6">
      <form method="GET" class="d-flex">
        <input type="hidden" name="page" value="1">
        <input type="hidden" name="modo" value="<?php echo $modo; ?>">
        <input class="form-control me-2" type="text" name="search"
          placeholder="<?php echo $modo === 'zoeiro' ? 'Digite algo aí' : 'Buscar por título'; ?>"
          value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="btn btn-primary" type="submit">Buscar</button>
      </form>
    </div>
 
    <!-- Filtro de ordenação -->
    <div class="col-md-6 text-end">
      <form method="GET" class="d-inline">
        <input type="hidden" name="page" value="<?php echo $pagina_atual; ?>">
        <input type="hidden" name="modo" value="<?php echo $modo; ?>">
        <select name="sort" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
          <option value="title.asc" <?php echo $ordenacao === 'title.asc' ? 'selected' : ''; ?>>A-Z</option>
          <option value="vote_average.desc" <?php echo $ordenacao === 'vote_average.desc' ? 'selected' : ''; ?>>Melhor Avaliados</option>
          <option value="popularity.desc" <?php echo $ordenacao === 'popularity.desc' ? 'selected' : ''; ?>>Mais Populares</option>
        </select>
      </form>
    </div>
  </div>
 
  <!-- Lista de filmes -->
  <div class="row" id="lista-filmes">
    <?php
    $tem_resultado = false;
    foreach ($filmes as $filme):
      if ($busca && strpos(strtolower($filme->title), $busca) === false) continue;
      $tem_resultado = true;
    ?>
      <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
        <div class="movie-item text-center card p-2 shadow-sm">
          <a href="detalhes.php?id=<?php echo htmlspecialchars($filme->id); ?>">
            <img src="<?php echo htmlspecialchars($base_image_url . $filme->poster_path); ?>" class="img-fluid rounded small-poster" alt="<?php echo htmlspecialchars($filme->title); ?>">
          </a>
          <h6 class="mt-2 movie-title-list"><?php echo htmlspecialchars($filme->title); ?></h6>
        </div>
      </div>
    <?php endforeach; ?>
 
    <?php if (!$tem_resultado): ?>
      <div class="col-12 text-center mt-5">
        <div class="alert alert-<?php echo $modo === 'zoeiro' ? 'warning' : 'info'; ?>">
          <?php echo $modo === 'zoeiro' ? '😅 Ops, não achei nada! Digita direito aí 🤡' : 'Nenhum filme encontrado para sua busca.'; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
 
  <!-- Paginação -->
  <nav aria-label="Paginação de Filmes">
    <ul class="pagination justify-content-center mt-4">
      <li class="page-item <?php echo ($pagina_atual <= 1) ? 'disabled' : ''; ?>">
        <a class="page-link" href="?page=<?php echo $pagina_atual - 1; ?>&sort=<?php echo $ordenacao; ?>&search=<?php echo $busca; ?>&modo=<?php echo $modo; ?>">
          <?php echo $modo === 'zoeiro' ? '← Voltar de Ré' : 'Anterior'; ?>
        </a>
      </li>
 
      <?php for ($i = max(1, $pagina_atual - 3); $i <= min($pagina_atual + 3, $total_paginas); $i++): ?>
        <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $ordenacao; ?>&search=<?php echo $busca; ?>&modo=<?php echo $modo; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php endfor; ?>
 
      <li class="page-item <?php echo ($pagina_atual >= $total_paginas) ? 'disabled' : ''; ?>">
        <a class="page-link" href="?page=<?php echo $pagina_atual + 1; ?>&sort=<?php echo $ordenacao; ?>&search=<?php echo $busca; ?>&modo=<?php echo $modo; ?>">
          <?php echo $modo === 'zoeiro' ? 'Avançar com Estilo →' : 'Próxima'; ?>
        </a>
      </li>
    </ul>
  </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 