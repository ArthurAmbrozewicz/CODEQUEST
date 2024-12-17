function openModal(desafioId) {
    const modal = document.getElementById('responseModal');
    const modalRespostas = document.getElementById('modal-respostas');
    modal.style.display = 'block';
    modalRespostas.innerHTML = 'Carregando respostas...';

    fetch(`fetch_respostas.php?id_desafio=${desafioId}`)
        .then(response => response.json())
        .then(respostas => {
            modalRespostas.innerHTML = ''; // Limpar 'Carregando respostas...'
            if (respostas.length > 0) {
                respostas.forEach(resposta => {
                    const p = document.createElement('p');
                    p.textContent = resposta.resposta;
                    const inputFeedback = document.createElement('input');
                    inputFeedback.type = 'text';
                    inputFeedback.placeholder = 'Escreva seu feedback aqui...';
                    const sendButton = document.createElement('button');
                    sendButton.textContent = 'Enviar Feedback';
                    sendButton.onclick = () => submitFeedback(resposta.id_resposta, inputFeedback.value);
                    modalRespostas.append(p, inputFeedback, sendButton);
                });
            } else {
                modalRespostas.textContent = 'Nenhuma resposta encontrada.';
            }
        })
        .catch(error => {
            console.error('Erro ao buscar respostas:', error);
            modalRespostas.textContent = 'Erro ao carregar respostas.';
        });
}

function closeModal() {
    document.getElementById('responseModal').style.display = 'none';
}

function submitFeedback(idResposta, feedback) {
    console.log(`Enviando feedback para a resposta com ID ${idResposta}: ${feedback}`);
    const formData = new FormData();
    formData.append('id_resposta', idResposta);
    formData.append('feedback', feedback);

    fetch('submit_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Falha ao enviar feedback');
        return response.text();
    })
    .then(data => {
        console.log('Feedback enviado com sucesso:', data);
        alert("Feedback enviado com sucesso!");
        location.reload()
    })
    .catch(error => {
        console.error('Erro ao enviar feedback:', error);
        alert("Erro ao enviar feedback: " + error.message);
    });
}
