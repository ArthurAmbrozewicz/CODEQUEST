document.addEventListener('DOMContentLoaded', () => {
    const todosGrupos = document.getElementById('todos-grupos');

    /**
     * Carrega todos os grupos disponíveis e atualiza o DOM.
     */
    const carregarGrupos = async () => {
        try {
            console.log('Carregando grupos...');
            const response = await fetch('http://localhost/code_quest/php/carregar_todos_grupos.php');
            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
            const grupos = await response.json();

            console.log('Grupos carregados:', grupos);

            todosGrupos.innerHTML = `
                <h4>Entre em grupos para compartilhar ideias.</h4>
                ${grupos.length > 0
                    ? grupos.map(grupo => `
                        <div class="grupo" id="grupo-${grupo.id}">
                            <h3>${grupo.nome_grupo}</h3>
                            <p>${grupo.descricao}</p>
                            <p><strong>Máximo de integrantes:</strong> ${grupo.max_integrantes}</p>
                            <button class="btn-entrar" data-id="${grupo.id}">Entrar no Grupo</button>
                            <button class="btn-chat" data-id="${grupo.id}">Chat</button>
                        </div>
                    `).join('')
                    : '<p>Nenhum grupo disponível no momento.</p>'}
            `;

            adicionarEventosAosBotoes();
        } catch (error) {
            console.error('Erro ao carregar os grupos:', error);
            alert('Erro ao carregar os grupos. Verifique sua conexão ou tente novamente.');
        }
    };

    /**
     * Adiciona eventos para os botões "Entrar no Grupo" e "Chat".
     */
    const adicionarEventosAosBotoes = () => {
        // Botões de entrar no grupo
        document.querySelectorAll('.btn-entrar').forEach(button => {
            button.addEventListener('click', async () => {
                const grupoId = button.dataset.id;
                const alunoId = 1; // Substitua por um ID real ou dinâmico

                if (!confirm(`Deseja entrar no grupo ID ${grupoId}?`)) return;

                try {
                    const response = await fetch('http://localhost/code_quest/php/entrar_grupo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ grupo_id: grupoId, aluno_id: alunoId })
                    });

                    const result = await response.json();
                    if (response.ok && result.success) {
                        alert(result.success || 'Você entrou no grupo com sucesso!');
                    } else {
                        throw new Error(result.error || 'Erro ao entrar no grupo.');
                    }
                } catch (error) {
                    console.error('Erro ao entrar no grupo:', error);
                    alert(error.message || 'Erro ao entrar no grupo. Tente novamente.');
                }
            });
        });

        // Botões de chat
        document.querySelectorAll('.btn-chat').forEach(button => {
            button.addEventListener('click', async () => {
                const grupoId = button.dataset.id;
                const grupoNome = document.querySelector(`#grupo-${grupoId} h3`).innerText;

                abrirChat(grupoId, grupoNome);
            });
        });
    };

    /**
     * Abre o chat do grupo.
     */
    const abrirChat = async (grupoId, grupoNome) => {
        const chatModal = document.getElementById('chat-modal');
        const mensagensDiv = document.getElementById('mensagens');
        const enviarMensagemBtn = document.getElementById('enviar-mensagem');
        const novaMensagemInput = document.getElementById('nova-mensagem');

        document.getElementById('chat-grupo-nome').innerText = `Chat do Grupo: ${grupoNome}`;
        chatModal.style.display = 'block';

        const carregarMensagens = async () => {
            try {
                const response = await fetch(`http://localhost/code_quest/php/carregar_mensagens.php?grupo_id=${grupoId}`);
                if (!response.ok) throw new Error('Erro ao carregar mensagens.');
                const mensagens = await response.json();

                mensagensDiv.innerHTML = mensagens.map(msg => `
                    <p><strong>${msg.nome}:</strong> ${msg.mensagem} <span style="font-size: 0.8em;">(${msg.data_envio})</span></p>
                `).join('');
            } catch (error) {
                console.error('Erro ao carregar mensagens:', error);
            }
        };

        enviarMensagemBtn.onclick = async () => {
            const mensagem = novaMensagemInput.value.trim();
            if (!mensagem) return;

            try {
                const response = await fetch('http://localhost/code_quest/php/salvar_mensagem.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ grupo_id: grupoId, aluno_id: 1, mensagem }) // Substitua aluno_id por valor dinâmico
                });

                if (!response.ok) throw new Error('Erro ao enviar mensagem.');

                novaMensagemInput.value = '';
                carregarMensagens();
            } catch (error) {
                console.error('Erro ao enviar mensagem:', error);
            }
        };

        document.getElementById('fechar-chat').onclick = () => {
            chatModal.style.display = 'none';
        };

        carregarMensagens();
    };

    carregarGrupos();
});
