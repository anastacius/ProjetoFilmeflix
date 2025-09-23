<!-- Geraldo história -->
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
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$genero_id = 36;
$idioma = 'pt-BR';
$ordenacao = 'title.asc';

$url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&with_genres={$genero_id}&page={$pagina_atual}&language={$idioma}&sort_by={$ordenacao}";


$json_string = @file_get_contents($url);

if ($json_string === false) {
    die('<div class="alert alert-danger" role="alert">Erro ao se conectar com a API do TMDB.</div>');
}

$filmes_data = json_decode($json_string);

if (!$filmes_data || !isset($filmes_data->results)) {
    die('<div class="alert alert-warning" role="alert">Nenhum filme encontrado.</div>');
}

$filmes = $filmes_data->results;
$total_paginas = $filmes_data->total_pages;

$base_image_url = 'https://image.tmdb.org/t/p/w500';

?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Filmes de Ação</h1>
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