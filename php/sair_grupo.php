<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$database = "db_codequest";

// Criar a conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

// Receber os dados enviados
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['grupo_id']) || !isset($input['aluno_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos enviados. Certifique-se de enviar grupo_id e aluno_id."]);
    exit;
}

$grupo_id = (int)$input['grupo_id'];
$aluno_id = (int)$input['aluno_id'];

if ($grupo_id <= 0 || $aluno_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "IDs inválidos. Certifique-se de que grupo_id e aluno_id sejam positivos."]);
    exit;
}

// Remover o aluno do grupo
$sql = "DELETE FROM grupo_alunos WHERE grupo_id = ? AND aluno_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $grupo_id, $aluno_id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Você saiu do grupo com sucesso!"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao sair do grupo."]);
}

$stmt->close();
$conn->close();
?>
