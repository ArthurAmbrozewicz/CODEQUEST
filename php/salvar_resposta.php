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

    // Decodifica os dados recebidos no corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['pergunta_id']) && isset($data['resposta']) && !empty(trim($data['resposta']))) {
        // Inicia a transação
        $pdo->beginTransaction();

        // Insere a resposta na tabela 'respostas'
        $stmt = $pdo->prepare("INSERT INTO respostas (pergunta_id, resposta, data_resposta) VALUES (:pergunta_id, :resposta, NOW())");
        $stmt->bindParam(':pergunta_id', $data['pergunta_id'], PDO::PARAM_INT);
        $stmt->bindParam(':resposta', $data['resposta'], PDO::PARAM_STR);
        $stmt->execute();

        // Marca a pergunta como respondida na tabela 'perguntas'
        $updateStmt = $pdo->prepare("UPDATE perguntas SET respondida = TRUE WHERE id = :pergunta_id");
        $updateStmt->bindParam(':pergunta_id', $data['pergunta_id'], PDO::PARAM_INT);
        $updateStmt->execute();

        // Confirma a transação
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Resposta salva e pergunta marcada como respondida!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    }
} catch (PDOException $e) {
    // Em caso de erro, desfaz a transação
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>
