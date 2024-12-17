<?php
$host = "localhost:3306";
$usuario = "root"; // Usuário do MySQL
$senha = ""; // Senha do MySQL
$database = "db_codequest";

// Conexão com o banco de dados
$conexao = new mysqli($host, $usuario, $senha, $database);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    $email = trim($_POST['email']);
    $github = !empty($_POST['github']) ? trim($_POST['github']) : null;
    $linkedin = !empty($_POST['linkedin']) ? trim($_POST['linkedin']) : null;
    $tipo_usuario = $_POST['tipo_usuario'];

    // Define a tabela de acordo com o tipo de usuário
    $tabela = ($tipo_usuario === "aluno") ? "aluno" : "professor";

    // Verifica se o nome de usuário ou o email já existem
    $query = "SELECT usuario, email FROM $tabela WHERE usuario = ? OR email = ?";
    $stmt = $conexao->prepare($query);
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conexao->error);
    }

    $stmt->bind_param("ss", $usuario, $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Verifica se o conflito é de nome de usuário ou email
        $linha = $resultado->fetch_assoc();
        if ($linha['usuario'] === $usuario) {
            echo "Erro: O nome de usuário já está em uso.";
        } elseif ($linha['email'] === $email) {
            echo "Erro: O email já está em uso.";
        }
    } else {
        // Prepara e executa a inserção na tabela correspondente
        $stmt->close();

        $stmt = $conexao->prepare("INSERT INTO $tabela (usuario, senha, email, github, linkedin) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Erro ao preparar a inserção: " . $conexao->error);
        }

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha
        $stmt->bind_param("sssss", $usuario, $senha_hash, $email, $github, $linkedin);

        if ($stmt->execute()) {
            // Redireciona para a página de login após cadastro
            header("Location: /code_quest/html/login.html");
            exit(); // Certifica-se de que o script será encerrado
        } else {
            echo "Erro ao realizar o cadastro: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conexao->close();
?>
