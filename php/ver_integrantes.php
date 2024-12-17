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

// Verifica se o grupo_id foi enviado
if (!isset($_GET['grupo_id']) || empty($_GET['grupo_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID do grupo não fornecido."]);
    exit;
}

$grupo_id = (int)$_GET['grupo_id'];

// Consulta os integrantes do grupo
$sql = "SELECT a.id_aluno, a.nome 
        FROM grupo_alunos ga
        JOIN alunos a ON ga.aluno_id = a.id_aluno
        WHERE ga.grupo_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();

$integrantes = [];
while ($row = $result->fetch_assoc()) {
    $integrantes[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($integrantes);
?>
