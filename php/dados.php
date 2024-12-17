<?php
session_start();

// Conexão com o banco de dados
$host = "localhost:3306";
$usuario = "root";  
$senha = "";        
$database = "db_codequest";  

$conexao = new mysqli($host, $usuario, $senha, $database);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tabela'])) {
    header("Location: login.php"); 
    exit();
}

// Determina o ID e a tabela com base na sessão
$id_usuario = $_SESSION['id_usuario'];
$tabela = $_SESSION['tabela'];

// Consulta para recuperar informações do usuário
$sql = "SELECT usuario, linkedin, github, email FROM $tabela WHERE id_{$tabela} = ?";

if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($nome, $linkedin, $github, $email);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Erro ao preparar a consulta: " . $conexao->error);
}

$conexao->close();

// Passa os dados para o arquivo de exibição
$data = [
    'usuario' => $nome,
    'linkedin' => $linkedin,
    'github' => $github,
    'email' => $email
];
?>
