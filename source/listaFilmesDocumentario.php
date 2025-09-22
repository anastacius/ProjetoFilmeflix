<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$genero_id = 99; // Documentário
$idioma = 'pt-BR';

$termo_busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$titulo_pagina = 'Documentários';
$parametros_paginacao = '';

if (!empty($termo_busca)) {
    $query_busca = urlencode($termo_busca); 
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$api_key}&query={$query_busca}&page={$pagina_atual}&language={$idioma}";
    $titulo_pagina = 'Resultados para: "' . htmlspecialchars($termo_busca) . '"';
    $parametros_paginacao = '&busca=' . $query_busca;
} else {
    $ordenacao = 'title.asc';
    $url = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&with_genres={$genero_id}&page={$pagina_atual}&language={$idioma}&sort_by={$ordenacao}";
}

$json_string = @file_get_contents($url);

if ($json_string === false) {
    die('<div class="alert alert-danger" role="alert">Erro ao se conectar com a API do TMDB.</div>');
}

$filmes_data = json_decode($json_string);

$filmes = [];
$total_paginas = 0;
if ($filmes_data && isset($filmes_data->results)) {
    $filmes = $filmes_data->results;
    $total_paginas = $filmes_data->total_pages;
}

// NOVO: Bloco de filtragem dos resultados da busca
if (!empty($termo_busca) && !empty($filmes)) {
    $filmes_filtrados = [];
    foreach ($filmes as $filme) {
        // A API retorna uma lista de IDs de gênero para cada filme.
        // Verificamos se o ID de Documentário (99) está nessa lista.
        if (isset($filme->genre_ids) && in_array($genero_id, $filme->genre_ids)) {
            $filmes_filtrados[] = $filme;
        }
    }
    // Substituímos a lista original de filmes pela lista já filtrada.
    $filmes = $filmes_filtrados;
}
?>

<div class="container mt-5">
    
    <h1 class="text-center mb-4"><?php echo $titulo_pagina; ?></h1>

    <form action="" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="busca" class="form-control" placeholder="Buscar em documentários..." value="<?php echo htmlspecialchars($termo_busca); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <div class="row">
        <?php if (!empty($filmes)): ?>
            <?php foreach ($filmes as $filme): ?>
                <?php if (!empty($filme->poster_path)): ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                        <div class="movie-item text-center">
                            <a href="detalhes.php?id=<?php echo htmlspecialchars($filme->id); ?>">
                                <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($filme->poster_path); ?>" class="img-fluid rounded small-poster" alt="<?php echo htmlspecialchars($filme->title); ?>">
                            </a>
                            <h5 class="mt-2 movie-title-list"><?php echo htmlspecialchars($filme->title); ?></h5>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Nenhum documentário encontrado com este critério.
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_paginas > 1): ?>
    <nav aria-label="Paginação de Filmes">
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item <?php echo ($pagina_atual <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $pagina_atual - 1; ?><?php echo $parametros_paginacao; ?>">Anterior</a>
            </li>
            
            <?php for ($i = max(1, $pagina_atual - 4); $i <= min($pagina_atual + 4, $total_paginas); $i++): ?>
                <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $parametros_paginacao; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo ($pagina_atual >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $pagina_atual + 1; ?><?php echo $parametros_paginacao; ?>">Próxima</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

</body>
</html>