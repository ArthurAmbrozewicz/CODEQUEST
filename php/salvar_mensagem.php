<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$usuario = "root";
$senha = "";
$database = "db_codequest";

// Conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['grupo_id']) || !isset($input['aluno_id']) || !isset($input['mensagem'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados inválidos enviados."]);
    exit;
}

$grupo_id = (int)$input['grupo_id'];
$aluno_id = (int)$input['aluno_id'];
$mensagem = $conn->real_escape_string($input['mensagem']);

$sql = "INSERT INTO mensagens_grupo (grupo_id, aluno_id, mensagem) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("iis", $grupo_id, $aluno_id, $mensagem);

if ($stmt->execute()) {
    echo json_encode(["success" => "Mensagem enviada com sucesso!"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao salvar a mensagem."]);
}

$stmt->close();
$conn->close();
?>
