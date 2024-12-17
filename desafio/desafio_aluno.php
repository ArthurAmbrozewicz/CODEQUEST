<?php
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

// Consulta para buscar os desafios
$sql = "SELECT id, nome, descricao, linguagem, dificuldade FROM desafios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="desafio_aluno.css">
    <title>Desafios Disponíveis</title>
</head>
<body>
<div id="nav-bar">
        <div id="nav-header"><a id="nav-title" href="#">CODE<span>QUEST</span></a></div>
        <div id="nav-content">
            <a href="/code_quest/aluno/modulos.html" class="nav-button">
                <i class="fas fa-cubes"></i>
                <span>Módulos</span>
            </a>
            <a href="/code_quest/aluno/userpdf.html" class="nav-button">
                <i class="fas fa-file-pdf"></i>
                <span>PDFS</span>
            </a>
            <a href="/code_quest/html/home.html" class="nav-button">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="/code_quest/aluno/ajuda.html" class="nav-button">
                <i class="fas fa-question-circle"></i>
                <span>Ajuda</span>
            </a>
            <a href="/code_quest/aluno/perfila.php" class="nav-button">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>
            <a href="/code_quest/desafio/desafio_aluno.php" class="nav-button">
                <i class="fas fa-user"></i>
                <span>Desafios Semanais</span>
            </a>
            <a href="/code_quest/desafio/vizualizar_feedbacks.html" class="nav-button">
                <i class="fas fa-user"></i>
                <span>Feedbacks desafios</span>
            </a>
            <a href="/code_quest/aluno/grupos_alunos.html" class="nav-button">
                <i class="fas fa-user"></i>
                <span>Grupo de alunos</span>
            </a>
            <div id="nav-content-highlight"></div>
        </div>
    </div>
    <h1>Desafios Disponíveis</h1>
    <div class= "desafio">

        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <h2><?= htmlspecialchars($row['nome']); ?></h2>
                        <p><?= htmlspecialchars($row['descricao']); ?></p>
                        <p>Linguagem: <?= htmlspecialchars($row['linguagem']); ?>  <br>Dificuldade: <?= htmlspecialchars($row['dificuldade']); ?></p>
                        <form action="submit_resposta.php" method="post">
                            <input type="hidden" name="id_desafio" value="<?= $row['id']; ?>">
                            <textarea name="resposta" required placeholder="Digite sua resposta aqui..."></textarea> <br>
                            <button type="submit">Enviar Resposta</button>
                        </form>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <?php else: ?>
                    <p>Nenhum desafio disponível.</p>
                    <?php endif; ?>
    </div>
    <a href="index.php">Voltar</a>
</body>
</html>
