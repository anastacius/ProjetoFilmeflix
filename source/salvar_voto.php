<?php
// Define que a resposta será no formato JSON
header('Content-Type: application/json');

// O nome do nosso arquivo de votos
$file_path = 'votes.json';

// Só executa o código se a requisição for do tipo POST (mais seguro)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega os dados enviados pelo JavaScript
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Valida se recebemos o id do filme e a nota
    if (isset($input['movieId']) && isset($input['rating'])) {
        $movie_id = (int)$input['movieId'];
        $rating = (int)$input['rating'];

        // Garante que a nota esteja entre 1 e 5
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Nota inválida.']);
            exit;
        }

        // Lê o conteúdo atual do arquivo votes.json
        // Se o arquivo não existir ou estiver vazio, começa com um array vazio
        $votes = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];
        if (!is_array($votes)) {
            $votes = [];
        }

        // Adiciona ou atualiza o voto para o filme específico
        // A chave do array será o ID do filme para acesso rápido
        $votes[$movie_id] = $rating;

        // Converte o array de volta para JSON e salva no arquivo
        // JSON_PRETTY_PRINT deixa o arquivo mais fácil de ler
        if (file_put_contents($file_path, json_encode($votes, JSON_PRETTY_PRINT))) {
            // Se salvou com sucesso, envia uma resposta positiva
            echo json_encode(['success' => true, 'message' => 'Voto salvo com sucesso!']);
        } else {
            // Se falhou ao salvar, envia um erro
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o voto. Verifique as permissões do arquivo.']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>