<!--Giulia -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes de Ficção Científica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$idioma = 'pt-BR';
$genero_scifi = 878; // ID do gênero Ficção Científica no TMDB

// Captura pesquisa (se existir)
$pesquisa = isset($_GET['q']) ? trim($_GET['q']) : "";

// Se tiver pesquisa, usa endpoint de search + filtro manual pelo gênero
if (!empty($pesquisa)) {
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$api_key}&language={$idioma}&query=" . urlencode($pesquisa);
} else {
    // Se não tiver pesquisa, lista filmes de ficção científica
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&language={$idioma}&with_genres={$genero_scifi}";
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
    <h1 class="mb-4">Filmes de Ficção Científica</h1>

    <!-- Barra de pesquisa -->
    <form method="get" class="mb-4 d-flex">
        <input type="text" name="q" class="form-control me-2" placeholder="Buscar filme de Ficção Científica..." value="<?php echo htmlspecialchars($pesquisa); ?>">
        <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>

    <div class="row">
        <?php foreach ($dados->results as $filme): ?>
            <!-- Filtra manualmente para manter só Ficção Científica -->
            <?php
            $temSciFi = false;
            if (isset($filme->genre_ids)) {
                $temSciFi = in_array($genero_scifi, $filme->genre_ids);
            }
            if (!$temSciFi) continue;
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
</div>

</body>
</html>
