<?php
$host = 'localhost:3306';
$database = 'db_codequest';
$usuario = 'root';
$senha = '';

$conexao = new mysqli($host, $usuario, $senha, $database);
if ($conexao->connect_error) {
    http_response_code(500);
    die("Falha na conexão com o banco de dados.");
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Consulta o banco para obter o arquivo
    $stmt = $conexao->prepare("SELECT nome_arquivo, dados FROM arquivos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"" . $file['nome_arquivo'] . "\"");
        echo $file['dados'];
    } else {
        http_response_code(404);
        echo "Arquivo não encontrado.";
    }
} else {
    http_response_code(400);
    echo "ID inválido ou não fornecido.";
}

$conexao->close();
?>
