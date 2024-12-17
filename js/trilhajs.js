let lives = 5; // Vidas do aluno, inicializado com o valor máximo
document.addEventListener('DOMContentLoaded', function () {
    const progressBar = document.querySelector('.progress');
    const hearts = document.querySelectorAll('.lives i');

    let progress = 0; // Progresso inicial
    let lives = 5;    // Vidas iniciais

    // Função para aumentar a barra de progresso
    function increaseProgress(amount) {
        if (progress < 100) {
            progress += amount;
            if (progress > 100) progress = 100; // Limite de 100%
            progressBar.style.width = progress + '%';
        }
    }

    // Função para perder uma vida
    function loseLife() {
        if (lives > 0) {
            hearts[lives - 1].classList.add('lost');
            lives--;
        } else {
            alert('Você perdeu todas as vidas!');
        }
    }

    // Função para carregar o número de vidas do banco de dados
    function fetchLives() {
        fetch('../fetch_lives.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    lives = data.lives; // Atualiza o número de vidas com o valor do banco de dados
                    updateLivesDisplay(); // Atualiza a interface com o número correto de vidas
                } else {
                    console.error("Erro ao buscar o número de vidas:", data.message);
                }
            })
            .catch(error => console.error("Erro ao buscar o número de vidas:", error));
    }

    // Função para atualizar a exibição das vidas na interface
    function updateLivesDisplay() {
        for (let i = 1; i <= 5; i++) {
            const heart = document.getElementById(`life${i}`);
            if (i <= lives) {
                heart.classList.remove('lost'); // Remove a classe 'lost' para exibir a vida ativa
            } else {
                heart.classList.add('lost'); // Adiciona a classe 'lost' para exibir a vida perdida
            }
        }
    }

    // Chama fetchLives ao carregar a página para inicializar as vidas corretamente
    document.addEventListener("DOMContentLoaded", () => {
        fetchLives(); // Carrega o número de vidas do banco de dados ao iniciar
    });


});

