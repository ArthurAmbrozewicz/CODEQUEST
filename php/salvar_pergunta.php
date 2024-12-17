<?php
header('Content-Type: application/json');

// Configuração do banco de dados
$host = 'localhost'; // Substitua se necessário
$db = 'db_codequest'; // Nome do banco de dados
$user = 'root'; // Usuário do banco
$password = ''; // Senha do banco

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decodifica os dados recebidos do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode([
            'success' => false,
            'message' => 'Nenhum dado recebido ou dados mal formatados.',
            'debug' => file_get_contents('php://input') // Exibe o corpo recebido para depuração
        ]);
        exit;
    }

    // Verifica se a pergunta foi enviada
    if (isset($data['pergunta']) && !empty(trim($data['pergunta']))) {
        $pergunta = trim($data['pergunta']);

        // Insere a pergunta no banco de dados
        $stmt = $pdo->prepare("INSERT INTO perguntas (texto, data_envio) VALUES (:texto, NOW())");
        $stmt->bindParam(':texto', $pergunta, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Pergunta salva com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao salvar a pergunta no banco de dados.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Pergunta inválida ou vazia.',
            'debug' => $data
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com o banco de dados.',
        'debug' => $e->getMessage() // Mensagem de erro do banco
    ]);
}
?>
