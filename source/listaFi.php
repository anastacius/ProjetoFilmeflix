<!-- GIULIA -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes de Romance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$idioma = 'pt-BR';
$genero_romance = 10749; // ID do gênero Romance no TMDB

// URL para buscar filmes do gênero Romance
$url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&language={$idioma}&with_genres={$genero_romance}";

$json_string = @file_get_contents($url);

if ($json_string === false) {
    die('<div class="alert alert-danger" role="alert">Erro ao buscar filmes de Romance.</div>');
}

$dados = json_decode($json_string);

if (!$dados || empty($dados->results)) {
    die('<div class="alert alert-warning" role="alert">Nenhum filme de Romance encontrado.</div>');
}

$base_image_url = 'https://image.tmdb.org/t/p/w500';
?>

<div class="container mt-5">
    <h1 class="mb-4">Filmes de Romance</h1>
    <div class="row">
        <?php foreach ($dados->results as $filme): ?>
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
