<?php
session_start(); // Iniciar a sessão



$id_aluno = $_SESSION['id_usuario']; // ID do aluno logado

// Conexão com o banco de dados
$host = "localhost:3306";
$username = "root";
$password = "";
$dbname = "db_codequest";

// Criar conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Falha na conexão: " . $conn->connect_error]);
    exit;
}

// Consultar respostas associadas ao aluno logado
$sql = "
    SELECT 
        d.nome, 
        d.descricao, 
        r.resposta, 
        f.feedback 
    FROM desafios d
    JOIN respostas_desafio r ON d.id = r.id_desafio
    LEFT JOIN feedbacks f ON r.id_resposta = f.id_resposta
    WHERE r.id_aluno = ? -- Filtra respostas associadas ao aluno logado
";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao preparar a consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();

$dados = [];
while ($row = $result->fetch_assoc()) {
    $dados[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($dados);
