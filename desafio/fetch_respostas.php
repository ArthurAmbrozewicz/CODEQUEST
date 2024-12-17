<?php
session_start(); // Iniciar a sessão

// Configuração do banco de dados
$host = "localhost:3306";
$username = "root";
$password = "";
$dbname = "db_codequest";

// Criar conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o parâmetro id_desafio foi enviado
if (!isset($_GET['id_desafio']) || empty($_GET['id_desafio'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "O parâmetro id_desafio é obrigatório."]);
    exit;
}

$id_desafio = (int)$_GET['id_desafio'];

// Preparar e executar a consulta para buscar as respostas do desafio
$sql = "SELECT id_resposta, resposta FROM respostas_desafio WHERE id_desafio = ? AND situacao IS NULL";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_desafio);
$stmt->execute();
$result = $stmt->get_result();

$respostas = [];
while ($row = $result->fetch_assoc()) {
    $respostas[] = [
        'id_resposta' => $row['id_resposta'],
        'resposta' => $row['resposta']
    ];
}

// Retornar as respostas em formato JSON
echo json_encode($respostas);

$stmt->close();
$conn->close();
?>
