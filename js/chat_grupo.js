document.addEventListener('DOMContentLoaded', () => {
    const chatModal = document.getElementById('chat-modal');
    const mensagensDiv = document.getElementById('mensagens');
    const novaMensagemInput = document.getElementById('nova-mensagem');
    const enviarMensagemBtn = document.getElementById('enviar-mensagem');
    const fecharChatBtn = document.getElementById('fechar-chat');

    let grupoAtualId = null;

    const abrirChat = async (grupoId, grupoNome) => {
        grupoAtualId = grupoId;
        document.getElementById('chat-grupo-nome').innerText = `Chat do Grupo: ${grupoNome}`;
        chatModal.style.display = 'block';
        carregarMensagens();
    };

    const carregarMensagens = async () => {
        try {
            const response = await fetch(`http://localhost/code_quest/php/carregar_mensagens.php?grupo_id=${grupoAtualId}`);
            if (!response.ok) throw new Error('Erro ao carregar mensagens.');
            const mensagens = await response.json();
            mensagensDiv.innerHTML = mensagens.map(msg => `
                <p><strong>${msg.nome}:</strong> ${msg.mensagem} <span style="font-size: 0.8em;">(${msg.data_envio})</span></p>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar mensagens:', error);
        }
    };

    enviarMensagemBtn.addEventListener('click', async () => {
        const mensagem = novaMensagemInput.value.trim();
        if (!mensagem) return;

        try {
            const response = await fetch('http://localhost/code_quest/php/salvar_mensagem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ grupo_id: grupoAtualId, aluno_id: 1, mensagem }) // Substituir aluno_id por valor dinâmico
            });

            if (!response.ok) throw new Error('Erro ao enviar mensagem.');

            novaMensagemInput.value = '';
            carregarMensagens();
        } catch (error) {
            console.error('Erro ao enviar mensagem:', error);
        }
    });

    fecharChatBtn.addEventListener('click', () => {
        chatModal.style.display = 'none';
        grupoAtualId = null;
    });

    document.querySelectorAll('.btn-chat').forEach(button => {
        button.addEventListener('click', (e) => {
            const grupoId = e.target.dataset.id;
            const grupoNome = e.target.closest('.grupo').querySelector('h3').innerText;
            abrirChat(grupoId, grupoNome);
        });
    });
});
