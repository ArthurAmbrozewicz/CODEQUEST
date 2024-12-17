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
require_once('../php/dados.php');
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
                        echo isset($_SESSION['id_aluno']) ? 'Aluno' : (isset($_SESSION['id_professor']) ? 'Professor' : 'ALUNO'); 
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