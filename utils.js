// ===== FUNÇÕES UTILITÁRIAS =====

// Selecionar todos os checkboxes da tabela
function selecionarTodos(checkbox) {
    document.querySelectorAll('.item-check').forEach(function (item) {
        item.checked = checkbox.checked;
    });
}

// Confirmação antes de excluir
function confirmarExclusao(nome) {
    return confirm('Deseja realmente excluir "' + nome + '"?');
}

// Exibir notificações na tela
function mostrarNotificacao(mensagem, tipo = 'info') {
    // Criar container de notificação se não existir
    let container = document.getElementById('notificacoes-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificacoes-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }

    // Criar notificação
    const notific = document.createElement('div');
    const icones = {
        'sucesso': 'fas fa-check-circle',
        'erro': 'fas fa-exclamation-circle',
        'aviso': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };

    const cores = {
        'sucesso': '#4caf50',
        'erro': '#f44336',
        'aviso': '#ff9800',
        'info': '#2196f3'
    };

    notific.style.cssText = `
        background-color: white;
        border-left: 4px solid ${cores[tipo] || cores['info']};
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease-out;
    `;

    const icone = document.createElement('i');
    icone.className = icones[tipo] || icones['info'];
    icone.style.color = cores[tipo] || cores['info'];
    icone.style.fontSize = '20px';

    const texto = document.createElement('span');
    texto.textContent = mensagem;
    texto.style.color = '#333';

    notific.appendChild(icone);
    notific.appendChild(texto);
    container.appendChild(notific);

    // Remover notificação após 4 segundos
    setTimeout(() => {
        notific.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            notific.remove();
        }, 300);
    }, 4000);
}

// Adicionar estilos das animações
if (!document.querySelector('style[data-utils-animations]')) {
    const style = document.createElement('style');
    style.setAttribute('data-utils-animations', 'true');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

// Formatar data
function formatarData(data) {
    if (typeof data === 'string') {
        data = new Date(data);
    }
    const dia = String(data.getDate()).padStart(2, '0');
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const ano = data.getFullYear();
    return dia + '/' + mes + '/' + ano;
}

// Formatar hora
function formatarHora(data) {
    if (typeof data === 'string') {
        data = new Date(data);
    }
    const horas = String(data.getHours()).padStart(2, '0');
    const minutos = String(data.getMinutes()).padStart(2, '0');
    return horas + ':' + minutos;
}

// Validar email
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Fazer requisição GET
async function fazerRequisicaoGET(url) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Erro na requisição GET:', error);
        throw error;
    }
}

// Fazer requisição POST
async function fazerRequisicaoPOST(url, dados) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Erro na requisição POST:', error);
        throw error;
    }
}

// Fazer requisição PUT
async function fazerRequisicaoPUT(url, dados) {
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Erro na requisição PUT:', error);
        throw error;
    }
}

// Fazer requisição DELETE
async function fazerRequisicaoDELETE(url) {
    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Erro na requisição DELETE:', error);
        throw error;
    }
}
