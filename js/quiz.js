let lastQuestionId = 0; // Variável para controlar o ID da última pergunta respondida
let lives = 5; // Vidas do aluno, inicializado com o valor máximo

document.addEventListener("DOMContentLoaded", () => {
  fetchLives(); // Carrega o número de vidas do banco de dados ao iniciar
  loadQuestion(); // Carrega a primeira pergunta
});

// Função para carregar o número de vidas do banco de dados
function fetchLives() {
  fetch('/code_quest/php/fetch_lives.php')
    .then(response => response.json())
    .then(data => {
      lives = data.lives; // Atualiza a quantidade de vidas com o valor do banco de dados
      updateLivesDisplay(); // Exibe as vidas na interface
    })
    .catch(error => console.error("Erro ao buscar o número de vidas:", error));
}

// Função para carregar a próxima pergunta
function loadQuestion() {
  fetch(`/code_quest/php/load_question.php?last_question_id=${lastQuestionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.end) {
        alert("Parabéns! Você completou todas as questões.");
      } else {
        displayQuestion(data);
      }
    })
    .catch(error => console.error("Erro ao carregar a pergunta:", error));
}

// Função para exibir a pergunta e as alternativas na página
function displayQuestion(data) {
  document.getElementById('question-number').innerText = data.question.question_id;
  document.getElementById('question-text').innerText = data.question.question_text;
  lastQuestionId = data.question.question_id; // Atualiza o ID da última pergunta

  const answersBox = document.getElementById('answers-box');
  answersBox.innerHTML = ''; // Limpa alternativas anteriores

  data.choices.forEach(choice => {
    const button = document.createElement('button');
    button.classList.add('answer-button');
    button.innerHTML = `<span class="btn-letter">${choice.label}</span> <span class="question-answer">${choice.text}</span>`;
    button.onclick = () => submitAnswer(choice.label, button);
    answersBox.appendChild(button);
  });
}

// Função para enviar a resposta selecionada ao servidor
function submitAnswer(selectedChoice, button) {
  const questionId = document.getElementById('question-number').innerText;

  // Desabilita todos os botões de resposta para evitar múltiplos cliques
  const answerButtons = document.querySelectorAll('.answer-button');
  answerButtons.forEach(btn => btn.disabled = true);

  fetch('/code_quest/php/check_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ question_id: questionId, selected_choice: selectedChoice })
  })
    .then(response => response.json())
    .then(data => {
      // Se a resposta estiver correta, marca o botão como verde; caso contrário, como vermelho
      if (data.correct) {
        button.classList.add('correct'); // Adiciona classe para resposta correta (verde)
      } else {
        button.classList.add('incorrect'); // Adiciona classe para resposta incorreta (vermelho)
        loseLife(); // Chama a função para perder uma vida
      }

      // Espera 1.5 segundos e carrega a próxima pergunta
      setTimeout(loadQuestion, 1500);
    })
    .catch(error => console.error("Erro ao verificar a resposta:", error));
}

// Função para perder uma vida
function loseLife() {
  if (lives > 0) {
    lives -= 1; // Decrementa o número de vidas
    updateLivesDisplay(); // Atualiza a exibição das vidas

    // Atualiza no banco de dados
    fetch('/code_quest/php/update_life.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ decrement: 1 }) // Envia o decremento
    })
      .then(response => response.json())
      .then(data => {
        if (!data.success) {
          console.error("Erro ao atualizar vida no banco:", data.message);
        }
      })
      .catch(error => console.error("Erro ao atualizar vida:", error));

    // Verifica se o usuário perdeu todas as vidas
    if (lives === 0) {
      alert("Você perdeu todas as vidas! Tente novamente.");
      disableQuiz(); // Desabilita o quiz se não houver mais vidas
    }
  }
}

function updateLivesDisplay() {
  console.log("Atualizando exibição das vidas. Total de vidas:", lives);
  for (let i = 1; i <= 5; i++) {
    const heart = document.getElementById(`life${i}`);
    if (heart) {
      console.log(`Alterando estado do coração com ID life${i}`);
      heart.classList.toggle('lost', i > lives); // Aplica classe 'lost' para corações além do número de vidas
    } else {
      console.error(`Elemento com ID life${i} não encontrado.`);
    }
  }
}



// Função para desabilitar o quiz
function disableQuiz() {
  const answerButtons = document.querySelectorAll('.answer-button');
  answerButtons.forEach(btn => btn.disabled = true);
}