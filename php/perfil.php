<?php
session_start();
require 'conexao.php'; // Arquivo que conecta ao banco de dados

// Verifique se o usuário está logado
if (!isset($_SESSION['tipo_usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login.html"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Determina a tabela com base no tipo de usuário
$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario_id = $_SESSION['usuario_id'];

if ($tipo_usuario === 'aluno') {
    $query = "SELECT nome, email, linkedin, github FROM aluno WHERE id = ?";
} else {
    $query = "SELECT nome, email, linkedin, github FROM professor WHERE id = ?";
}

$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit();
}

// Inclui o arquivo HTML para exibir o perfil
include 'perfil.html';
?>
