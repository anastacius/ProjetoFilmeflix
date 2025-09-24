<!-- Base atualizada -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes de Ação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$quantidade = isset($_GET['quantidade']) ? (int)$_GET['quantidade'] : 20;
$quantidade = in_array($quantidade, [20, 30, 50]) ? $quantidade : 20;

$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$genero_id = 28; // ação
$idioma = 'pt-BR';
$ordenacao = 'title.asc';

$ordenacao_selecionada = $_GET['ordenacao'] ?? 'alfabetica';
switch ($ordenacao_selecionada) {
    case 'popularidade':
        $ordenacao = 'popularity.desc';
        break;
    case 'melhores_notas':
        $ordenacao = 'vote_average.desc';
        break;
    case 'alfabetica':
    default:
        $ordenacao = 'title.asc';
        break;
}

$termo_busca = $_GET['busca'] ?? '';

$titulo_pagina = 'Filmes de Ação';
$parametros_paginacao = "&quantidade={$quantidade}";

$filmes = [];
$total_paginas = 1;
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// Buscar múltiplas páginas se necessário
$filmes_coletados = [];
$filmes_por_pagina_api = 20;
$paginas_necessarias = ceil($quantidade / $filmes_por_pagina_api);

for ($p = 0; $p < $paginas_necessarias; $p++) {
    $pagina_api = $pagina_atual + $p;
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&with_genres={$genero_id}&page={$pagina_api}&language={$idioma}&sort_by={$ordenacao}";
    $json_string = @file_get_contents($url);

    if ($json_string === false) {
        die('<div class="alert alert-danger" role="alert">Erro ao se conectar com a API do TMDB.</div>');
    }

    $filmes_data = json_decode($json_string);

    if (!$filmes_data || !isset($filmes_data->results)) {
        die('<div class="alert alert-warning" role="alert">Nenhum filme encontrado.</div>');
    }

    $total_paginas = $filmes_data->total_pages;
    foreach ($filmes_data->results as $filme) {
        $filmes_coletados[] = $filme;
        if (count($filmes_coletados) >= $quantidade) break 2;
    }
}

$filmes = $filmes_coletados;

// Filtragem por busca (se necessário)
if (!empty($termo_busca) && !empty($filmes)) {
    $filmes_filtrados = [];
    foreach ($filmes as $filme) {
        if (isset($filme->genre_ids) && in_array($genero_id, $filme->genre_ids)) {
            $filmes_filtrados[] = $filme;
        }
    }
    $filmes = $filmes_filtrados;
}
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Filmes de Ação</h1>

    <form action="" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="busca" class="form-control" placeholder="Buscar em Ação..." value="<?php echo htmlspecialchars($termo_busca); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <form method="GET" class="mb-4">
        <input type="hidden" name="busca" value="<?php echo htmlspecialchars($termo_busca); ?>">
        <div class="row align-items-center">
            <div class="col-auto">
                <label for="quantidade" class="form-label mb-0">Quantidade de filmes:</label>
            </div>
            <div class="col-auto">
                <select name="quantidade" id="quantidade" class="form-select" onchange="this.form.submit()">
                    <option value="20" <?php echo ($quantidade == 20) ? 'selected' : ''; ?>>20</option>
                    <option value="30" <?php echo ($quantidade == 30) ? 'selected' : ''; ?>>30</option>
                    <option value="50" <?php echo ($quantidade == 50) ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
        </div>
    </form>
<form method="GET" class="mb-4">
    <input type="hidden" name="busca" value="<?php echo htmlspecialchars($termo_busca); ?>">
    <input type="hidden" name="quantidade" value="<?php echo $quantidade; ?>">
    <div class="row align-items-center">
        <div class="col-auto">
            <label for="ordenacao" class="form-label mb-0">Ordenar por:</label>
        </div>
        <div class="col-auto">
            <select name="ordenacao" id="ordenacao" class="form-select" onchange="this.form.submit()">
                <option value="alfabetica" <?php echo ($ordenacao_selecionada == 'alfabetica') ? 'selected' : ''; ?>>Ordem Alfabética</option>
                <option value="popularidade" <?php echo ($ordenacao_selecionada == 'popularidade') ? 'selected' : ''; ?>>Popularidade</option>
                <option value="melhores_notas" <?php echo ($ordenacao_selecionada == 'melhores_notas') ? 'selected' : ''; ?>>Melhores Notas</option>
            </select>
        </div>
    </div>
</form>

<div class="row">


    <div class="row">
        <?php foreach ($filmes as $filme): ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                <div class="movie-item text-center">
                    <a href="detalhes.php?id=<?php echo htmlspecialchars($filme->id); ?>">
                        <img src="<?php echo htmlspecialchars($base_image_url . $filme->poster_path); ?>" class="img-fluid rounded small-poster" alt="<?php echo htmlspecialchars($filme->title); ?>">
                    </a>
                    <h5 class="mt-2 movie-title-list"><?php echo htmlspecialchars($filme->title); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <nav aria-label="Paginação de Filmes">
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item <?php echo ($pagina_atual <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $pagina_atual - 1; ?>">Anterior</a>
            </li>
            
            <?php for ($i = max(1, $pagina_atual - 8); $i <= min($pagina_atual + 8, $total_paginas); $i++): ?>
                <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo ($pagina_atual >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $pagina_atual + 1; ?>">Próxima</a>
            </li>
        </ul>
    </nav>
</div>

</body>
</html>