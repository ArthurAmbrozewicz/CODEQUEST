<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$host = "localhost:3306";
$usuario = "root";
$senha = ""; 
$database = "db_codequest";

$conexao = new mysqli($host, $usuario, $senha, $database);

if ($conexao->connect_error) {
    die(json_encode(["success" => false, "message" => "Erro de conexão ao banco de dados: " . $conexao->connect_error]));
}

if (!isset($_SESSION['id_aluno'])) {
    echo json_encode(["success" => false, "message" => "Usuário não está logado"]);
    exit;
}

$aluno_id = intval($_SESSION['id_aluno']);

$result = $conexao->query("SELECT vida FROM aluno WHERE id_aluno = $aluno_id");

if ($result) {
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "lives" => (int)$row['vida']]);
    } else {
        echo json_encode(["success" => false, "message" => "Registro não encontrado para o aluno."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Erro ao executar consulta: " . $conexao->error]);
}

$conexao->close();
?>