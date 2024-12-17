<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Configuração do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$database = "db_codequest";

// Criar a conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $database);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodificar os dados recebidos em JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $grupo_id = $input['grupo_id'] ?? null;

    // Validar se o ID foi fornecido
    if ($grupo_id === null || !is_numeric($grupo_id)) {
        http_response_code(400);
        echo json_encode(["error" => "ID do grupo inválido."]);
        exit;
    }

    // Excluir o grupo pelo ID
    $sql = "DELETE FROM grupos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $grupo_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Grupo excluído com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir o grupo: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido."]);
}

$conn->close();
?>
