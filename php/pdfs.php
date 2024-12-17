<?php
$host = 'localhost:3306';
$database = 'db_codequest';
$usuario = 'root';
$senha = '';

$conexao = new mysqli($host, $usuario, $senha, $database);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $usuario, $senha);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi enviado e o arquivo foi selecionado
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['uploadpdfs'])) {
        $arquivo = $_FILES['uploadpdfs'];

        // Verifica se o arquivo é um PDF
        if ($arquivo['type'] != 'application/pdf') {
            echo "Por favor, envie apenas arquivos PDF.";
            exit;
        }

        // Lê o conteúdo do arquivo e prepara as informações para o banco de dados
        $nomeArquivo = $arquivo['name'];
        $tipoArquivo = $arquivo['type'];
        $tamanhoArquivo = $arquivo['size'];
        $dadosArquivo = file_get_contents($arquivo['tmp_name']);
        $assunto = $_POST['assunto'];

        // Inserção no banco de dados
        $sql = "INSERT INTO arquivos (nome_arquivo, tipo_arquivo, tamanho_arquivo, situacao, dados, assunto) VALUES (:nome, :tipo, :tamanho,:situacao, :dados,:assunto)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nomeArquivo);
        $stmt->bindParam(':tipo', $tipoArquivo);
        $stmt->bindParam(':tamanho', $tamanhoArquivo);
        $stmt->bindValue(':situacao', "Pendente");
        $stmt->bindParam(':dados', $dadosArquivo, PDO::PARAM_LOB);
        $stmt->bindValue(':assunto', "$assunto");

        if ($stmt->execute()) {
            echo "Arquivo enviado e salvo com sucesso!";
        } else {
            echo "Erro ao salvar o arquivo.";
        }
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
