async function fetchFiles() {
    try {
        const response = await fetch('/code_quest/php/pdfadmin.php'); 
        if (!response.ok) {
            throw new Error("Erro na resposta da requisição");
        }
        const files = await response.json();
        
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';

        files.forEach(file => {
            const listItem = document.createElement('li');
            listItem.classList.add("file-item");

            // Container para a pré-visualização
            const previewContainer = document.createElement('div');
            previewContainer.classList.add("preview");

            // Canvas para prévia do PDF
            const canvas = document.createElement('canvas');
            canvas.classList.add("preview-canvas");
            canvas.width = 100;
            const context = canvas.getContext('2d');

            // Renderiza o PDF como uma miniatura
            const loadingTask = pdfjsLib.getDocument(file.url);
            loadingTask.promise.then(pdf => {
                pdf.getPage(1).then(page => {
                    const scale = 0.2;
                    const viewport = page.getViewport({ scale });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    page.render({ canvasContext: context, viewport: viewport });
                });
            }).catch(error => {
                console.error("Erro ao carregar PDF:", error);
            });

            previewContainer.appendChild(canvas);
            listItem.appendChild(previewContainer);
            
            //assunto do pdf
            const assunto = document.createElement('p');
            assunto.classList.add("assunto-text");
            assunto.textContent = `assunto: ${file.assunto}`;
            listItem.appendChild(assunto)


            // Link para download
            const link = document.createElement('a');
            link.href = file.url;
            link.textContent = file.nome_arquivo;
            link.classList.add("download-link");
            listItem.appendChild(link);

            // Primeiro radio com label, no mesmo contêiner div
            const radioContainer1 = document.createElement('div');
            radioContainer1.classList.add("radio-container");
            const radioInput1 = document.createElement('input');
            radioInput1.type = "radio";
            radioInput1.name = `situacao_${file.id}`;
            radioInput1.value = "Aprovado";
            radioInput1.classList.add("file-radio");

            const label1 = document.createElement('label');
            label1.textContent = "Aprovar";
            radioContainer1.appendChild(radioInput1);
            radioContainer1.appendChild(label1);
            listItem.appendChild(radioContainer1);

            // Segundo radio com label, no mesmo contêiner div
            const radioContainer2 = document.createElement('div');
            radioContainer2.classList.add("radio-container");
            const radioInput2 = document.createElement('input');
            radioInput2.type = "radio";
            radioInput2.name = `situacao_${file.id}`;
            radioInput2.value = "Negado";
            radioInput2.classList.add("file-radio");

            const label2 = document.createElement('label');
            label2.textContent = "Negar";
            radioContainer2.appendChild(radioInput2);
            radioContainer2.appendChild(label2);
            listItem.appendChild(radioContainer2);

            // Botão de submit
            const submitButton = document.createElement('button');
            submitButton.type = 'button'; // Define como 'button' para evitar recarregar a página
            submitButton.textContent = 'Enviar';
            submitButton.addEventListener('click', async () => {
                const selectedOption = document.querySelector(`input[name="situacao_${file.id}"]:checked`);
                
                if (!selectedOption) {
                    alert("Selecione uma opção antes de enviar.");
                    return;
                }
                
                const situacao = selectedOption.value;
                
                // Envia a requisição para atualizar o banco de dados
                try {
                    const updateResponse = await fetch('/code_quest/php/mudarsituacao.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: file.id, situacao: situacao })
                    });
                    
                    const result = await updateResponse.json();
                    if (result.success) {
                        alert("Situação atualizada com sucesso!");
                        location.reload();
                    } else {
                        alert("Erro ao atualizar situação.");
                    }
                } catch (error) {
                    console.error("Erro na atualização:", error);
                }
            });
            listItem.appendChild(submitButton);

            fileList.appendChild(listItem);
        });
    } catch (error) {
        console.error("Erro ao buscar arquivos:", error);
    }
}

fetchFiles();
