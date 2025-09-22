<!-- GIULIA -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes por Gênero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$idioma  = 'pt-BR';

// 🔹 Lista de gêneros (id => nome)
$generos = [
    28    => "Ação",
    12    => "Aventura",
    16    => "Animação",
    35    => "Comédia",
    80    => "Crime",
    99    => "Documentário",
    18    => "Drama",
    10751 => "Família",
    14    => "Fantasia",
    36    => "História",
    27    => "Terror",
    10402 => "Música",
    9648  => "Mistério",
    10749 => "Romance",
    878   => "Ficção científica",
    10770 => "Cinema TV",
    53    => "Thriller",
    10752 => "Guerra",
    37    => "Faroeste"
];

// Gênero atual (padrão = Ficção científica)
$genero_id   = isset($_GET['genero']) ? (int)$_GET['genero'] : 878;
$genero_nome = $generos[$genero_id] ?? "Ficção científica";

// Captura pesquisa, categoria e página
$pesquisa   = isset($_GET['q']) ? trim($_GET['q']) : "";
$categoria  = isset($_GET['sort']) ? $_GET['sort'] : "popularity.desc";
$pagina     = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Monta URL da API
if (!empty($pesquisa)) {
    // Busca por título (filtra depois pelo gênero)
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$api_key}&language={$idioma}&query=" . urlencode($pesquisa) . "&page={$pagina}";
} else {
    // Lista padrão por gênero e categoria
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&language={$idioma}&with_genres={$genero_id}&sort_by={$categoria}&page={$pagina}";
}

$json_string = @file_get_contents($url);

if ($json_string === false) {
    die('<div class="alert alert-danger" role="alert">Erro ao buscar filmes.</div>');
}

$dados = json_decode($json_string);

if (!$dados || empty($dados->results)) {
    echo '<div class="alert alert-warning" role="alert">Nenhum filme encontrado.</div>';
    $dados->results = [];
}

$base_image_url = 'https://image.tmdb.org/t/p/w500';
?>

<div class="container mt-5">
    <h1 class="mb-4">Filmes de <?php echo $genero_nome; ?></h1>

    <!-- Formulário de pesquisa + filtros -->
    <form method="get" class="mb-4 row g-2">
        <div class="col-md-3">
            <select name="genero" class="form-select" onchange="this.form.submit()">
                <?php foreach ($generos as $id => $nome): ?>
                    <option value="<?php echo $id; ?>" <?php if ($id == $genero_id) echo "selected"; ?>>
                        <?php echo $nome; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <input type="text" name="q" class="form-control" 
                   placeholder="Buscar título em <?php echo $genero_nome; ?>..." 
                   value="<?php echo htmlspecialchars($pesquisa); ?>">
        </div>

        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="popularity.desc" <?php if($categoria=="popularity.desc") echo "selected"; ?>>Mais populares</option>
                <option value="popularity.asc" <?php if($categoria=="popularity.asc") echo "selected"; ?>>Menos populares</option>
                <option value="vote_average.desc" <?php if($categoria=="vote_average.desc") echo "selected"; ?>>Melhor avaliados</option>
                <option value="vote_average.asc" <?php if($categoria=="vote_average.asc") echo "selected"; ?>>Pior avaliados</option>
                <option value="release_date.desc" <?php if($categoria=="release_date.desc") echo "selected"; ?>>Mais recentes</option>
                <option value="release_date.asc" <?php if($categoria=="release_date.asc") echo "selected"; ?>>Mais antigos</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="row">
        <?php foreach ($dados->results as $filme): ?>
            <!-- Filtra manualmente só para o gênero escolhido -->
            <?php
            $temGenero = false;
            if (isset($filme->genre_ids)) {
                $temGenero = in_array($genero_id, $filme->genre_ids);
            }
            if (!$temGenero) continue;
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if ($filme->poster_path): ?>
                        <img src="<?php echo $base_image_url . $filme->poster_path; ?>" class="card-img-top" alt="Pôster do Filme">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($filme->title); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($filme->release_date); ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($filme->overview, 0, 100)) . '...'; ?></p>
                    </div>
                    <div class="card-footer">
                        <span class="badge bg-success"><?php echo number_format($filme->vote_average, 1); ?> / 10</span>
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
                    <a class="page-link" href="?genero=<?php echo $genero_id; ?>&q=<?php echo urlencode($pesquisa); ?>&sort=<?php echo urlencode($categoria); ?>&page=<?php echo $pagina-1; ?>">Anterior</a>
                </li>
            <?php endif; ?>

            <?php if ($pagina < $dados->total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?genero=<?php echo $genero_id; ?>&q=<?php echo urlencode($pesquisa); ?>&sort=<?php echo urlencode($categoria); ?>&page=<?php echo $pagina+1; ?>">Próxima</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

</body>
</html>
