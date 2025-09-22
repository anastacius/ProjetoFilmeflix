<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Filme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('<div class="alert alert-danger" role="alert">ID do filme não fornecido.</div>');
}

$filme_id = (int)$_GET['id'];
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$idioma = 'pt-BR';

$url = "https://api.themoviedb.org/3/movie/{$filme_id}?api_key={$api_key}&language={$idioma}";

$json_string = @file_get_contents($url);

if ($json_string === false) {
    die('<div class="alert alert-danger" role="alert">Erro ao buscar os detalhes do filme.</div>');
}

$filme = json_decode($json_string);

if (!$filme) {
    die('<div class="alert alert-warning" role="alert">Detalhes do filme não encontrados.</div>');
}

$base_image_url = 'https://image.tmdb.org/t/p/w500';
$poster_url = $base_image_url . $filme->poster_path;


$generos_array = [];
if (isset($filme->genres)) {
    foreach ($filme->genres as $genero) {
        $generos_array[] = $genero->name;
    }
}
$generos_string = implode(', ', $generos_array);

?>
    <div class="container mt-5">
        <div class="card p-4 movie-card">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="<?php echo htmlspecialchars($poster_url); ?>" class="img-fluid poster-img" alt="Pôster do Filme">
                </div>
                <div class="col-md-8">
                    <h1 class="movie-title"><?php echo htmlspecialchars($filme->title); ?></h1>
                    <p class="text-muted"><?php echo htmlspecialchars($filme->release_date); ?></p>
                    
                    <div class="mt-4">
                        <h4>Sinopse</h4>
                        <p class="lead"><?php echo htmlspecialchars($filme->overview); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($generos_string); ?></p>
                    </div>

                    <div class="mt-4">
                        <h4>Avaliação Média</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>TMDB Score</strong>
                                <span class="badge bg-success rounded-pill"><?php echo number_format($filme->vote_average, 1); ?> / 10</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php'); ?>" class="btn btn-primary">Voltar para a Lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>