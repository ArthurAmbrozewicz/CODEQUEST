<?php
$host = 'localhost:3306';
$database = 'db_codequest';
$usuario = 'root';
$senha = '';

$conexao = new mysqli($host, $usuario, $senha, $database);
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Consulta para listar os arquivos
$query = "SELECT id, nome_arquivo, situacao, assunto FROM arquivos";
$result = $conexao->query($query);

$files = [];
    
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['situacao'] == 'Pendente'){
            $files[] = [
                "id" => $row['id'],
                'nome_arquivo' => $row['nome_arquivo'],
                'url' => "download.php?id=" . $row['id'],
                'assunto' => $row['assunto'] 
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($files);

$conexao->close();
?>
