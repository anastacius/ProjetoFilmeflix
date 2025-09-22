<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes Ficção Científica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body class="sc-body">

<?php
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$idioma  = 'pt-BR';

// Gênero Ficção científica
$genero_id   = 878;
$genero_nome = "Ficção científica";

// Captura pesquisa, categoria e página
$pesquisa   = isset($_GET['q']) ? trim($_GET['q']) : "";
$categoria  = isset($_GET['sort']) ? $_GET['sort'] : "popularity.desc";
$pagina     = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Monta URL da API
if (!empty($pesquisa)) {
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$api_key}&language={$idioma}&query=" . urlencode($pesquisa) . "&page={$pagina}";
} else {
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&language={$idioma}&with_genres={$genero_id}&sort_by={$categoria}&page={$pagina}";
}

$json_string = @file_get_contents($url);
if ($json_string === false) die('<div class="alert alert-danger" role="alert">Erro ao buscar filmes.</div>');

$dados = json_decode($json_string);
if (!$dados || empty($dados->results)) echo '<div class="alert alert-warning" role="alert">Nenhum filme encontrado.</div>';

$base_image_url = 'https://image.tmdb.org/t/p/w500';
?>

<div class="container mt-5">
    <h1 class="sc-title mb-4">Filmes de <?php echo $genero_nome; ?></h1>

    <!-- Formulário de pesquisa + filtros -->
    <form method="get" class="mb-4 row g-2">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control sc-form-control" placeholder="Buscar título em <?php echo $genero_nome; ?>..." value="<?php echo htmlspecialchars($pesquisa); ?>">
        </div>
        <div class="col-md-4">
            <select name="sort" class="form-select sc-form-select">
                <option value="popularity.desc" <?php if($categoria=="popularity.desc") echo "selected"; ?>>Mais populares</option>
                <option value="popularity.asc" <?php if($categoria=="popularity.asc") echo "selected"; ?>>Menos populares</option>
                <option value="vote_average.desc" <?php if($categoria=="vote_average.desc") echo "selected"; ?>>Melhor avaliados</option>
                <option value="vote_average.asc" <?php if($categoria=="vote_average.asc") echo "selected"; ?>>Pior avaliados</option>
                <option value="release_date.desc" <?php if($categoria=="release_date.desc") echo "selected"; ?>>Mais recentes</option>
                <option value="release_date.asc" <?php if($categoria=="release_date.asc") echo "selected"; ?>>Mais antigos</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn sc-btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="row">
        <?php foreach ($dados->results as $filme):
            $temGenero = in_array($genero_id, $filme->genre_ids ?? []);
            if (!$temGenero) continue;
        ?>
            <div class="col-md-3 mb-4">
                <div class="card sc-card h-100">
                    <?php if (!empty($filme->poster_path)): ?>
                        <img src="<?php echo $base_image_url . $filme->poster_path; ?>" class="card-img-top" alt="Pôster do Filme">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title sc-card-title"><?php echo htmlspecialchars($filme->title); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($filme->release_date); ?></p>
                        <p class="card-text sc-card-text"><?php echo htmlspecialchars(substr($filme->overview, 0, 100)) . '...'; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <nav aria-label="Navegação de página">
        <ul class="pagination justify-content-center mt-4">
            <?php if ($pagina > 1): ?>
                <li class="page-item">
                    <a class="page-link sc-page-link" href="?q=<?php echo urlencode($pesquisa); ?>&sort=<?php echo urlencode($categoria); ?>&page=<?php echo $pagina-1; ?>">Anterior</a>
                </li>
            <?php endif; ?>
            <?php if ($pagina < ($dados->total_pages ?? 1)): ?>
                <li class="page-item">
                    <a class="page-link sc-page-link" href="?q=<?php echo urlencode($pesquisa); ?>&sort=<?php echo urlencode($categoria); ?>&page=<?php echo $pagina+1; ?>">Próxima</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>
