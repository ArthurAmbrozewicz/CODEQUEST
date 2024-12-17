<?php
session_start(); // Iniciar a sessão

// Incluir conexão com o banco de dados
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

// Verificar se o ID do desafio e a resposta foram postados e se o ID do usuário está na sessão
if (isset($_POST['id_desafio'], $_POST['resposta']) && isset($_SESSION['id_usuario'])) {
    $id_desafio = $_POST['id_desafio'];
    $id_aluno = $_SESSION['id_usuario']; // Usar o ID do usuário da sessão
    $resposta = $_POST['resposta'];

    // Preparar a query SQL
    $sql = "INSERT INTO respostas_desafio (id_aluno, id_desafio, resposta) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Vincular parâmetros à declaração preparada
    $stmt->bind_param("iis", $id_aluno, $id_desafio, $resposta);

    // Executar a query
    if ($stmt->execute()) {
        echo "Resposta enviada com sucesso!";
    } else {
        echo "Erro ao enviar resposta: " . $stmt->error;
    }

    // Fechar conexões
    $stmt->close();
    $conn->close();
} else {
    echo "Dados insuficientes fornecidos ou usuário não logado.";
}

// Redirecionar de volta para a página de desafios
header("Location: desafio_aluno.php");
exit();
?>
