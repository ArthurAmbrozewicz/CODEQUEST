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
    die("Falha na conexão: " . $conn->connect_error);
}
// Recebe os dados do POST
$id_resposta = $_POST['id_resposta'];
$feedback_text = $_POST['feedback'];

// Prepara a conexão
if ($conn->connect_error) {
    http_response_code(500);
    echo "Falha na conexão: " . $conn->connect_error;
    exit;
}

// Busca o id_aluno associado à resposta
$sql = "SELECT id_aluno FROM respostas_desafio WHERE id_resposta = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar a consulta: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $id_resposta);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Nenhuma resposta encontrada com o ID fornecido.";
    exit;
}
$row = $result->fetch_assoc();
$id_aluno = $row['id_aluno'];

// Insere o feedback na tabela de feedbacks
$sql = "INSERT INTO feedbacks (id_resposta, id_aluno, feedback) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Erro ao preparar a consulta para inserir feedback: " . $conn->error;
    exit;
}

$stmt->bind_param("iis", $id_resposta, $id_aluno, $feedback_text);
// Após inserir o feedback com sucesso
if ($stmt->execute()) {
    // Atualiza a situação da resposta para "respondido"
    $updateSql = "UPDATE respostas_desafio SET situacao = 'respondido' WHERE id_resposta = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $id_resposta);
    if ($updateStmt->execute()) {
        echo "Feedback enviado com sucesso e situação atualizada.";
    } else {
        echo "Erro ao atualizar a situação: " . $updateStmt->error;
    }
    $updateStmt->close();
} else {
    echo "Erro ao enviar feedback: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
