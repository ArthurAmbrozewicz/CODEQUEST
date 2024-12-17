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

$conn = new mysqli($host, $usuario, $senha, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

// Receber os dados enviados pelo frontend
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['grupo_id']) || !isset($data['aluno_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos enviados. Certifique-se de enviar grupo_id e aluno_id."]);
    exit;
}

$grupo_id = (int)$data['grupo_id'];
$aluno_id = (int)$data['aluno_id'];

// Verificar se o aluno já está no grupo
$sql = "SELECT * FROM grupo_alunos WHERE grupo_id = ? AND aluno_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Você já está neste grupo."]);
    exit;
}

// Adicionar o aluno ao grupo
$sql = "INSERT INTO grupo_alunos (grupo_id, aluno_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $grupo_id, $aluno_id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Você entrou no grupo com sucesso!"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao entrar no grupo."]);
}

$stmt->close();
$conn->close();
