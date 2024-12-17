document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#perguntas-table tbody');

    // Função para carregar perguntas
    function carregarPerguntas() {
        fetch('http://localhost/code_quest/php/listar_perguntas.php') // Verifique o caminho correto
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = '';
                    data.data.forEach(pergunta => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${pergunta.id}</td>
                            <td>${pergunta.texto}</td>
                            <td>${pergunta.data_envio}</td>
                            <td>
                                ${pergunta.respondida
                                ? '<span>Respondida</span>'
                                : `<button class="responder-btn" data-id="${pergunta.id}">Responder</button>`
                            }
                                <button class="excluir-btn" data-id="${pergunta.id}">Excluir</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });

                    // Adicionar eventos aos botões de responder e excluir
                    document.querySelectorAll('.responder-btn').forEach(button => {
                        button.addEventListener('click', (event) => {
                            const perguntaId = event.target.getAttribute('data-id');
                            responderPergunta(perguntaId, event.target);
                        });
                    });

                    document.querySelectorAll('.excluir-btn').forEach(button => {
                        button.addEventListener('click', (event) => {
                            const perguntaId = event.target.getAttribute('data-id');
                            excluirPergunta(perguntaId);
                        });
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4">Nenhuma pergunta encontrada.</td></tr>';
                    console.error('Erro do servidor:', data.message);
                }
            })
            .catch(error => {
                tableBody.innerHTML = '<tr><td colspan="4">Erro na conexão com o servidor.</td></tr>';
                console.error('Erro na conexão:', error);
            });
    }

    // Função para responder uma pergunta
    function responderPergunta(perguntaId, buttonElement) {
        const resposta = prompt('Digite sua resposta para a pergunta:');
        if (resposta) {
            fetch('http://localhost/code_quest/php/salvar_resposta.php', { // Endpoint correto para salvar respostas
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pergunta_id: perguntaId, resposta: resposta }) // Campos esperados pelo PHP
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Resposta enviada com sucesso!');
                        // Atualizar o status da pergunta na interface
                        const cell = buttonElement.parentElement;
                        cell.innerHTML = '<span>Respondida</span>';
                    } else {
                        alert('Erro ao enviar a resposta: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na conexão:', error);
                    alert('Erro ao enviar a resposta.');
                });
        }
    }

    // Função para excluir uma pergunta
    function excluirPergunta(perguntaId) {
        if (confirm('Tem certeza de que deseja excluir esta pergunta?')) {
            fetch('http://localhost/code_quest/php/excluir_pergunta.php', { // Endpoint correto para excluir perguntas
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: perguntaId }) // Campo esperado pelo PHP
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pergunta excluída com sucesso!');
                        // Remover a pergunta da tabela
                        document.querySelector(`button[data-id="${perguntaId}"]`).closest('tr').remove();
                    } else {
                        alert('Erro ao excluir a pergunta: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na conexão:', error);
                    alert('Erro ao excluir a pergunta.');
                });
        }
    }

    // Carrega as perguntas quando a página é carregada
    carregarPerguntas();
});
