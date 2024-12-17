<?php
session_start(); // Iniciar a sessão para acessar as variáveis de sessão

// Configuração da conexão com o banco de dados
$host = "localhost:3306";
$username = "root";
$password = "";
$dbname = "db_codequest";

// Criar conexão
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar se o ID do professor está na sessão
if (!isset($_SESSION['id_usuario'])) {
    die("Erro: ID do professor não está disponível.");
}

$id_professor = $_SESSION['id_usuario']; // Atribuir ID do professor da sessão

// Receber dados do formulário
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$linguagem = $_POST['linguagem'];
$dificuldade = $_POST['dificuldade'];

if (empty($linguagem)) {
    die('Erro: O campo linguagem está vazio.');
}

// Preparar a query SQL
$sql = "INSERT INTO desafios (id_professor, nome, descricao, linguagem, dificuldade) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("issss", $id_professor, $nome, $descricao, $linguagem, $dificuldade);

// Executar a query
if ($stmt->execute()) {
    echo "Desafio inserido com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fechar conexões
$stmt->close();
$conn->close();
?>
