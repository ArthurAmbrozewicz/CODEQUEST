document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-criar-grupo');
    const meusGrupos = document.getElementById('meus-grupos');
    const modalIntegrantes = document.getElementById('modal-integrantes');
    const listaIntegrantes = document.getElementById('lista-integrantes');
    const fecharModalIntegrantes = document.getElementById('fechar-modal-integrantes');


    /**
     * Função para carregar os grupos dinamicamente e atualizar o DOM.
     */
    const carregarGrupos = async () => {
        try {
            console.log('Carregando grupos...');
            const response = await fetch('http://localhost/code_quest/php/carregar_grupo.php');
            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

            const grupos = await response.json();
            console.log('Grupos carregados:', grupos);

            meusGrupos.innerHTML = `
                <h4>Meus Grupos</h4>
                ${grupos.length > 0
                    ? grupos.map(grupo => `
                        <div class="grupo" id="grupo-${grupo.id}">
                            <h3>${grupo.nome_grupo}</h3>
                            <p>${grupo.descricao}</p>
                            <p><strong>Máximo de integrantes:</strong> ${grupo.max_integrantes}</p>
                            <button class="btn-excluir" data-id="${grupo.id}">Excluir Grupo</button>
                            <button class="btn-integrantes" data-id="${grupo.id}">Ver Integrantes</button>
                            <button class="btn-chat" data-id="${grupo.id}">Chat</button>
                        </div>
                    `).join('')
                    : '<p>Você ainda não criou nenhum grupo.</p>'}
            `;

            adicionarEventosAosBotoes();
        } catch (error) {
            console.error('Erro ao carregar os grupos:', error);
            alert('Erro ao carregar os grupos. Verifique a conexão ou tente novamente.');
        }
    };
    const exibirIntegrantes = (grupoNome, integrantes) => {
        // Atualiza o título do modal
        document.getElementById('nome-grupo-modal').innerText = `Integrantes de ${grupoNome}`;

        // Atualiza a lista de integrantes
        listaIntegrantes.innerHTML = integrantes.length > 0
            ? integrantes.map(i => `<p>${i.nome}</p>`).join('')
            : '<p>Este grupo ainda não possui integrantes.</p>';

        // Exibe o modal
        modalIntegrantes.style.display = 'flex';
    };

    /**
     * Adiciona eventos para os botões "Excluir", "Ver Integrantes" e "Chat".
     */
    const adicionarEventosAosBotoes = () => {
        // Botões de excluir grupo
        document.querySelectorAll('.btn-excluir').forEach(button => {
            button.addEventListener('click', async () => {
                const grupoId = button.dataset.id;
                if (!confirm('Tem certeza de que deseja excluir este grupo?')) return;

                try {
                    const response = await fetch('../php/excluir_grupo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ grupo_id: grupoId })
                    });

                    const result = await response.json();
                    if (response.ok) {
                        const grupoElement = document.getElementById(`grupo-${grupoId}`);
                        grupoElement.style.transition = 'opacity 0.3s';
                        grupoElement.style.opacity = '0';
                        setTimeout(() => grupoElement.remove(), 300);
                        alert(result.success || 'Grupo excluído com sucesso.');
                    } else {
                        throw new Error(result.error || 'Erro ao excluir o grupo.');
                    }
                } catch (error) {
                    console.error('Erro ao excluir o grupo:', error);
                    alert('Erro ao excluir o grupo. Tente novamente.');
                }
            });
        });

        document.querySelectorAll('.btn-integrantes').forEach(button => {
            button.addEventListener('click', async () => {
                const grupoId = button.dataset.id;
                const grupoNome = document.querySelector(`#grupo-${grupoId} h3`).innerText;

                try {
                    console.log(`Carregando integrantes para o grupo ${grupoId}...`);
                    const response = await fetch(`../php/ver_integrantes.php?grupo_id=${grupoId}`);
                    if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

                    const integrantes = await response.json();
                    exibirIntegrantes(grupoNome, integrantes);
                } catch (error) {
                    console.error(`Erro ao carregar os integrantes do grupo ${grupoId}:`, error);
                    alert('Erro ao carregar os integrantes. Tente novamente.');
                }
            });
        });

        // Botões de ver integrantes
        document.querySelectorAll('.btn-integrantes').forEach(button => {
            button.addEventListener('click', async () => {
                const grupoId = button.dataset.id;

                try {
                    console.log(`Carregando integrantes para o grupo ${grupoId}...`);
                    const response = await fetch(`../php/ver_integrantes.php?grupo_id=${grupoId}`);
                    if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

                    const integrantes = await response.json();
                    console.log(`Integrantes do grupo ${grupoId}:`, integrantes);

                    if (integrantes.length > 0) {
                        const nomes = integrantes.map(i => i.nome).join(', ');
                    } else {
                        alert(`O grupo ${grupoId} não possui integrantes no momento.`);
                    }
                } catch (error) {
                    console.error(`Erro ao carregar os integrantes do grupo ${grupoId}:`, error);
                    alert('Erro ao carregar os integrantes. Tente novamente.');
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

    fecharModalIntegrantes.onclick = () => {
        modalIntegrantes.style.display = 'none';
    };

    /**
     * Submissão do formulário para criar um novo grupo.
     */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            const formData = new FormData(form);
            const response = await fetch('../php/criar_grupo.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
            const result = await response.json();

            alert(result.success || 'Grupo criado com sucesso!');
            form.reset();
            carregarGrupos(); // Atualiza a lista de grupos
        } catch (error) {
            console.error('Erro ao criar o grupo:', error);
            alert('Erro ao criar o grupo. Tente novamente.');
        }
    });

    // Carrega os grupos ao carregar a página
    carregarGrupos();
});
