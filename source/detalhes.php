<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Filme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>

<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('<div class="container mt-4"><div class="alert alert-danger" role="alert">ID do filme não fornecido.</div></div>');
}

$filme_id = (int)$_GET['id'];
$api_key = '304354587f5fcd1ae0898cf39f4dc337'; // Sua chave de API
$idioma = 'pt-BR';

// --- 1. Busca os detalhes principais do filme ---
$details_url = "https://api.themoviedb.org/3/movie/{$filme_id}?api_key={$api_key}&language={$idioma}";
$details_json = @file_get_contents($details_url);

if ($details_json === false) {
    die('<div class="container mt-4"><div class="alert alert-danger" role="alert">Erro ao buscar os detalhes do filme.</div></div>');
}
$filme = json_decode($details_json);
if (!$filme) {
    die('<div class="container mt-4"><div class="alert alert-warning" role="alert">Detalhes do filme não encontrados.</div></div>');
}

// --- 2. Busca os créditos (elenco) do filme ---
$credits_url = "https://api.themoviedb.org/3/movie/{$filme_id}/credits?api_key={$api_key}&language={$idioma}";
$credits_json = @file_get_contents($credits_url);
$elenco = [];
if ($credits_json !== false) {
    $credits_data = json_decode($credits_json);
    if ($credits_data && isset($credits_data->cast)) {
        $elenco = array_slice($credits_data->cast, 0, 10); // Pega os 10 primeiros do elenco
    }
}

// --- 3. Prepara as informações para exibição ---
$base_image_url = 'https://image.tmdb.org/t/p/w500';
$poster_url = $filme->poster_path ? $base_image_url . $filme->poster_path : 'https://via.placeholder.com/500x750.png?text=Pôster+Indisponível';
$tagline = $filme->tagline ?? '';

// Formata a lista de gêneros
$generos_string = implode(', ', array_map(fn($g) => $g->name, $filme->genres ?? []));

// Formata a duração do filme para "Xh Ym"
$runtime_string = '';
if (isset($filme->runtime) && $filme->runtime > 0) {
    $horas = floor($filme->runtime / 60);
    $minutos = $filme->runtime % 60;
    $runtime_string = "{$horas}h {$minutos}m";
}
?>

<div class="container mt-5 mb-5">
    <div class="card p-4 shadow-sm">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <img src="<?php echo htmlspecialchars($poster_url); ?>" class="img-fluid rounded" alt="Pôster de <?php echo htmlspecialchars($filme->title); ?>">
            </div>

            <div class="col-md-8">
                <h1 class="fw-bold"><?php echo htmlspecialchars($filme->title); ?></h1>
                
                <?php if (!empty($tagline)): ?>
                    <p class="fst-italic text-muted">"<?php echo htmlspecialchars($tagline); ?>"</p>
                <?php endif; ?>

                <p class="text-muted">
                    <span><?php echo date('Y', strtotime($filme->release_date)); ?></span>
                    <?php if (!empty($runtime_string)): ?>
                        <span class="mx-2">•</span>
                        <span><?php echo htmlspecialchars($runtime_string); ?></span>
                    <?php endif; ?>
                </p>
                <p><strong>Gênero:</strong> <?php echo htmlspecialchars($generos_string); ?></p>

                <div class="mt-4">
                    <h4>Sinopse</h4>
                    <p class="lead"><?php echo htmlspecialchars($filme->overview) ?: 'Sinopse não disponível.'; ?></p>
                </div>

                <div class="mt-4">
                     <h4>Avaliação</h4>
                     <p>
                        <span class="badge bg-success rounded-pill fs-6 align-middle">
                           <?php echo number_format($filme->vote_average, 1); ?> / 10
                        </span> 
                        <span class="text-muted ms-2 align-middle">(Baseado em <?php echo number_format($filme->vote_count, 0, '', '.'); ?> votos)</span>
                     </p>
                </div>

                <?php if (!empty($elenco)): ?>
                <div class="mt-4">
                    <h4>Elenco Principal</h4>
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-3">
                        <?php foreach ($elenco as $ator): ?>
                            <div class="col">
                                <div class="card h-100 text-center border-0">
                                    <?php 
                                        $ator_img = $ator->profile_path 
                                            ? $base_image_url . $ator->profile_path 
                                            : 'https://via.placeholder.com/185x278.png?text=Sem+Foto'; 
                                    ?>
                                    <img src="<?php echo htmlspecialchars($ator_img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($ator->name); ?>">
                                    <div class="card-body p-2">
                                        <p class="card-title fw-bold mb-0" style="font-size: 0.9rem;"><?php echo htmlspecialchars($ator->name); ?></p>
                                        <p class="card-text text-muted" style="font-size: 0.8rem;"><?php echo htmlspecialchars($ator->character); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-5">
                    <a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php'); ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>