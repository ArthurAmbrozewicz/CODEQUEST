<?php
header('Content-Type: application/json');

// Configuração do banco de dados
$host = 'localhost';
$db = 'db_codequest';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para buscar apenas perguntas com respostas
    $stmt = $pdo->query("
        SELECT p.id AS pergunta_id, p.texto AS pergunta, 
               r.resposta, r.data_resposta 
        FROM perguntas p
        JOIN respostas r ON p.id = r.pergunta_id
        ORDER BY r.data_resposta DESC
    ");
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupa perguntas e respostas
    $faq = [];
    foreach ($dados as $linha) {
        $id = $linha['pergunta_id'];
        if (!isset($faq[$id])) {
            $faq[$id] = [
                'pergunta' => $linha['pergunta'],
                'respostas' => []
            ];
        }
        $faq[$id]['respostas'][] = $linha['resposta'];
    }

    echo json_encode(['success' => true, 'data' => array_values($faq)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar perguntas: ' . $e->getMessage()]);
}
?>
