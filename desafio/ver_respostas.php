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

$id_professor = $_SESSION['id_usuario'];

// Consulta para buscar todos os desafios criados pelo professor logado
$sql = "SELECT id, nome, descricao FROM desafios WHERE id_professor = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_professor);
$stmt->execute();
$result = $stmt->get_result();

// Início do HTML
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desafios Criados</title>
    <link rel="stylesheet" href="ver_respostas.css"> <!-- Adicione o link para seu arquivo CSS aqui -->
</head>
<body>
<div id="nav-bar">
<div id="nav-bar">
        <div id="nav-header"><a id="nav-title" href="#">CODE<span>QUEST</span></a></div>
        <div id="nav-content">
            <a href="criar_grupo.html" class="nav-button">
                <i class="fas fa-cubes"></i>
                <span>GRUPO DE ALUNOS</span>
            </a>
            <a href="/code_quest/professor/pdfs.html" class="nav-button">
                <i class="fas fa-file-pdf"></i>
                <span>ENVIAR PDF</span>
            </a>
            <a href="/code_quest/professor/perfilp.php" class="nav-button">
                <i class="fas fa-file-pdf"></i>
                <span>PERFIL</span>
            </a>
            <a href="/code_quest/desafio/desafio.html" class="nav-button">
                <i class="fas fa-file-pdf"></i>
                <span>CRIAR DESAFIO</span>
            </a>
            <a href="/code_quest/desafio/ver_respostas.php" class="nav-button">
                <i class="fas fa-file-pdf"></i>
                <span>RESPOSTAS DESAFIOS</span>
            </a>
            <div id="nav-content-highlight"></div>
        </div>
    </div>
    </div>
    <header>
        <h1>Meus Desafios</h1>
    </header>
    <main>
    <div class="respostas">
        <?php
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<a href='#' onclick='openModal(" . $row['id'] . ")'>" . htmlspecialchars($row['nome']) . "</a> - " . htmlspecialchars($row['descricao']);
                echo " <button onclick='openModal(" . $row['id'] . ")'>Ver Respostas</button>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum desafio encontrado.</p>";
        }
        ?>
    </div>

    <div id="responseModal" class="modal">
    <div class="modal-content">
        <h4>Respostas do Desafio</h4>
        <div id="modal-respostas"></div> <!-- As respostas serão inseridas aqui -->
    </div>
    <div class="modal-footer">
        <button onclick="closeModal()">Fechar</button>
    </div>
</div>

    </main>
</body>
<script src ="modal.js"></script>
</html>

<?php
$stmt->close();
$conn->close();
?>
