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

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

// Recupera todos os grupos criados
$sql = "SELECT id, nome_grupo, descricao, max_integrantes FROM grupos";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $grupos = [];
    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }
    echo json_encode($grupos);
} else {
    // Retorna um array vazio se não houver grupos
    echo json_encode([]);
}

$conn->close();
?>
