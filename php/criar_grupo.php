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

// Processa os dados do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_grupo = $_POST['nome_grupo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $max_integrantes = isset($_POST['max_integrantes']) ? (int)$_POST['max_integrantes'] : 0;

    // Validações simples
    if (empty($nome_grupo) || empty($descricao) || $max_integrantes <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Por favor, preencha todos os campos corretamente."]);
        exit;
    }

    // Insere o grupo no banco de dados
    $sql = "INSERT INTO grupos (nome_grupo, descricao, max_integrantes) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssi", $nome_grupo, $descricao, $max_integrantes);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Grupo criado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar o grupo: " . $stmt->error]);
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
