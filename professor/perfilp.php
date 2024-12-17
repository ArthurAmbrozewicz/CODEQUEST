<?php
echo "<pre>";
print_r($data);
echo "</pre>";

?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
require_once '../php/dados.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeQuest</title>
    <link rel="stylesheet" href="/code_quest/css/perfil.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css'>
</head>

<body>
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

    <div class="profile-content">
        <div class="main-infos">
            <div class="profile-photo">
                <img src="<?php echo isset($data['foto_perfil']) ? htmlspecialchars($data['foto_perfil']) : '/code_quest/img/image.png'; ?>" alt="Foto do perfil">

            </div>
            <div class="main-name">
                <p>
                    <?php echo isset($data['usuario']) ? htmlspecialchars($data['usuario']) : 'Nome não disponível'; ?>
                </p>
            </div>
            <div class="occupation-info">
                <p class="occupation-box">
                    <?php 
                        echo isset($_SESSION['id_aluno']) ? 'Aluno' : (isset($_SESSION['id_professor']) ? 'Professor' : 'PROFESSOR'); 
                    ?>
                </p>
            </div>
            <div class="social-media-info">
                <div class="social-box">
                    <a href="<?php echo !empty($data['linkedin']) ? htmlspecialchars($data['linkedin']) : '#'; ?>" target="_blank">
                        <i class="fab fa-linkedin"></i>
                        <?php echo !empty($data['linkedin']) ? htmlspecialchars($data['linkedin']) : 'LinkedIn não disponível'; ?>
                    </a>
                </div>
                <div class="social-box">
                    <a href="<?php echo !empty($data['github']) ? htmlspecialchars($data['github']) : '#'; ?>" target="_blank">
                        <i class="fab fa-github"></i>
                        <?php echo !empty($data['github']) ? htmlspecialchars($data['github']) : 'GitHub não disponível'; ?>
                    </a>
                </div>
                <div class="social-box">
                    <a><i class="fas fa-envelope"></i>
                        <?php echo isset($data['email']) ? htmlspecialchars($data['email']) : 'Email não disponível'; ?>
                    </a>
                </div>
            </div>
        </div>        
    </div>
</body>

</html>