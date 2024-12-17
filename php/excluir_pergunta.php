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

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM perguntas WHERE id = :id");
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Pergunta excluída com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID da pergunta ausente.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>
