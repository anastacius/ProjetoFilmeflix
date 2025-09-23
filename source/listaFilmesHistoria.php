<!-- Geraldo história -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmes de História</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="./styles/style.css">
</head>
<body>


<?php
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$quantidade = isset($_GET['quantidade']) ? (int)$_GET['quantidade'] : 20;
$quantidade = in_array($quantidade, [20, 30, 50]) ? $quantidade : 20;

$api_key = '304354587f5fcd1ae0898cf39f4dc337';
$genero_id = 36; // História
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

$titulo_pagina = 'Filmes de História';
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