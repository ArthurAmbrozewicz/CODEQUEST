<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão
session_start();

$host = "localhost:3306";
$usuario = "root";
$senha = ""; 
$database = "db_codequest";

$conexao = new mysqli($host, $usuario, $senha, $database);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Função para verificar o usuário em uma tabela específica
function verificar_usuario($conexao, $usuario, $senha, $tabela) {
    $query = "SELECT * FROM $tabela WHERE usuario = ?";
    $stmt = $conexao->prepare($query);
    
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conexao->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();

    if ($resultado->num_rows > 0) {
        $user = $resultado->fetch_assoc();
        if (password_verify($senha, $user['senha'])) {
            return $user; // Retorna os dados do usuário se o login for bem-sucedido
        }
    }
    return null; // Retorna null se o login falhar
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    // Verifica nas tabelas alunos, professores e curadores
    $usuarioAluno = verificar_usuario($conexao, $usuario, $senha, "aluno");
    $usuarioProfessor = verificar_usuario($conexao, $usuario, $senha, "professor");
    $usuarioCurador = verificar_usuario($conexao, $usuario, $senha, "curador");

    if ($usuarioAluno) {
        $_SESSION['usuario'] = $usuarioAluno['usuario'];
        $_SESSION['id_usuario'] = $usuarioAluno['id_aluno'];
        $_SESSION['tabela'] = "aluno";
        header("Location: /code_quest/aluno/modulos.html");
        exit();
    } elseif ($usuarioProfessor) {
        $_SESSION['usuario'] = $usuarioProfessor['usuario'];
        $_SESSION['id_usuario'] = $usuarioProfessor['id_professor'];
        $_SESSION['tabela'] = "professor";
        header("Location: /code_quest/professor/perfilp.php");
        exit();
    } elseif ($usuarioCurador) {
        $_SESSION['usuario'] = $usuarioCurador['usuario'];
        $_SESSION['id_usuario'] = $usuarioCurador['id_curador'];
        $_SESSION['tabela'] = "curador";
        header("Location: /code_quest/curador/admin_perguntas.html");
        exit();
    } else {
        // Redireciona de volta ao login com mensagem de erro
        header("Location: /code_quest/html/login.html?error=1");
        exit();
    }
}

$conexao->close();
?>
