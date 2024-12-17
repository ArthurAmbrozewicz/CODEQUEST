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

if (!isset($_GET['grupo_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID do grupo não fornecido."]);
    exit;
}

$grupo_id = (int)$_GET['grupo_id'];

$sql = "SELECT mg.mensagem, a.nome, mg.data_envio 
        FROM mensagens_grupo mg
        JOIN alunos a ON mg.aluno_id = a.id_aluno
        WHERE mg.grupo_id = ?
        ORDER BY mg.data_envio ASC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($mensagens);
?>
