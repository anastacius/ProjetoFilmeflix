<!-- matheus -->

<?php
// --- CONFIGURAÇÕES INICIAIS ---
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Lembre-se de manter sua chave da API segura.
$api_key = '304354587f5fcd1ae0898cf39f4dc337'; 
$genero_id = 37;
$idioma = 'pt-BR';

// --- CAPTURA DE INPUTS DO USUÁRIO ---
$termo_busca = isset($_GET['busca']) ? $_GET['busca'] : '';
// Opção de ordenação com 'popularity.desc' como padrão
$ordenacao_selecionada = isset($_GET['ordenacao']) ? $_GET['ordenacao'] : 'popularity.desc';

// --- LÓGICA PARA MONTAR A URL DA API ---
$titulo_pagina = 'Faroeste';
$parametros_paginacao = '&ordenacao=' . urlencode($ordenacao_selecionada);
$url = '';

if (!empty($termo_busca)) {
    // MODO BUSCA: A ordenação é ignorada pela API, que sempre usa relevância.
    $query_busca = urlencode($termo_busca);
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$api_key}&query={$query_busca}&page={$pagina_atual}&language={$idioma}";
    $titulo_pagina = 'Resultados para: "' . htmlspecialchars($termo_busca) . '"';
    $parametros_paginacao .= '&busca=' . $query_busca;
    
} else {
    // MODO DESCOBERTA (NAVEGAÇÃO): A ordenação é aplicada.
    $url_base_discover = "https://api.themoviedb.org/3/discover/movie?api_key={$api_key}&with_genres={$genero_id}&page={$pagina_atual}&language={$idioma}";
    
    // Constrói a URL com a ordenação selecionada
    $url = $url_base_discover . '&sort_by=' . urlencode($ordenacao_selecionada);
    
    // Caso especial para "Populares": adiciona filtro de contagem de votos
    if ($ordenacao_selecionada === 'popularity.desc') {
        $url .= '&vote_count.gte=1000'; // gte = Greater Than or Equal (Maior ou igual a 1000)
    }
}

// --- CHAMADA À API E PROCESSAMENTO DE DADOS ---
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

// Filtra os resultados da busca por gênero (só executa em modo busca)
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
<!DOCTYPE html>
<html lang="pt-br">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>faroeste</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/relapse-into-madness" rel="stylesheet">
    
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body class="page-faroeste">

<div class="faroeste-container">
    
    <h1 class="faroeste-titulo"><?php echo $titulo_pagina; ?></h1>
    
    <form action="" method="GET" class="faroeste-form">
        <div class="faroeste-form-row">
            <div class="faroeste-form-col-input">
                <input type="text" 
                       name="busca" 
                       class="faroeste-input-text" 
                       placeholder="Buscar em Faroeste..." 
                       value="<?php echo htmlspecialchars($termo_busca); ?>">
            </div>
            <div class="faroeste-form-col-select">
                <select name="ordenacao" class="faroeste-select">
                    <option value="popularity.desc" <?php echo ($ordenacao_selecionada == 'popularity.desc') ? 'selected' : ''; ?>>Populares</option>
                    <option value="vote_average.desc" <?php echo ($ordenacao_selecionada == 'vote_average.desc') ? 'selected' : ''; ?>>Melhores Votos</option>
                    <option value="original_title.asc" <?php echo ($ordenacao_selecionada == 'original_title.asc') ? 'selected' : ''; ?>>Ordem Alfabética (A-Z)</option>
                </select>
            </div>
            <div class="faroeste-form-col-btn">
                <button class="faroeste-btn" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="faroeste-filmes-grid">
        <?php if (!empty($filmes)): ?>
            <?php foreach ($filmes as $filme): ?>
                <?php if (!empty($filme->poster_path)): ?>
                    <div class="faroeste-filme-card">
                        <div class="faroeste-filme-inner"> 
                            <a href="detalhes.php?id=<?php echo htmlspecialchars($filme->id); ?>">
                                <img src="https://image.tmdb.org/t/p/w500<?php echo htmlspecialchars($filme->poster_path); ?>" 
                                     class="faroeste-filme-poster" 
                                     alt="<?php echo htmlspecialchars($filme->title); ?>">
                            </a>
                            <h5 class="faroeste-filme-titulo"><?php echo htmlspecialchars($filme->title); ?></h5>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="faroeste-alerta">
                Nenhum Filme de Faroeste encontrado com este critério.
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_paginas > 1): ?>
    <nav class="faroeste-paginacao">
        <ul class="faroeste-paginacao-lista">
            <li class="faroeste-paginacao-item <?php echo ($pagina_atual <= 1) ? 'desativado' : ''; ?>">
                <a class="faroeste-paginacao-link" 
                   href="?page=<?php echo $pagina_atual - 1; ?><?php echo $parametros_paginacao; ?>">Anterior</a>
            </li>

            <?php for ($i = max(1, $pagina_atual - 4); $i <= min($pagina_atual + 4, $total_paginas); $i++): ?>
                <li class="faroeste-paginacao-item <?php echo ($i == $pagina_atual) ? 'ativo' : ''; ?>">
                    <a class="faroeste-paginacao-link" 
                       href="?page=<?php echo $i; ?><?php echo $parametros_paginacao; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="faroeste-paginacao-item <?php echo ($pagina_atual >= $total_paginas) ? 'desativado' : ''; ?>">
                <a class="faroeste-paginacao-link" 
                   href="?page=<?php echo $pagina_atual + 1; ?><?php echo $parametros_paginacao; ?>">Próxima</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div>
