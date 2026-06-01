// ===== FUNÇÕES GERAIS DA APLICAÇÃO =====

// Função para exibir data formatada
function mostrarData() {
    const data = new Date();
    const dia = String(data.getDate()).padStart(2, '0');
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const ano = data.getFullYear();
    return dia + '/' + mes + '/' + ano;
}

// Função de saudação por hora
function saudacao() {
    const hora = new Date().getHours();
    if (hora < 12) return 'Bom dia';
    if (hora < 18) return 'Boa tarde';
    return 'Boa noite';
}

// Obter URL base da API
function getApiBaseUrl() {
    return '/api';
}

// Função para obter dados do usuário logado
function getUsuarioLogado() {
    const usuarioStr = sessionStorage.getItem('usuario');
    return usuarioStr ? JSON.parse(usuarioStr) : null;
}

// Inicializar componentes ao carregar a página
document.addEventListener('DOMContentLoaded', function () {
    // Verificar se há usuário logado
    const usuario = getUsuarioLogado();
    if (!usuario) {
        window.location.href = 'index.html';
        return;
    }

    // Atualizar data no header
    const dataEl = document.querySelector('.date-display');
    if (dataEl) {
        dataEl.innerHTML = '<i class="far fa-calendar-alt"></i> ' + mostrarData();
    }

    // Adicionar saudação no welcome card
    const saudacaoEl = document.querySelector('.welcome-card h2');
    if (saudacaoEl) {
        const nomeUsuario = usuario.nome || 'Admin';
        saudacaoEl.textContent = saudacao() + ', ' + nomeUsuario + '!';
    }

    // Marcar menu ativo automaticamente
    const pagina = window.location.pathname.split('/').pop() || 'dashboard.html';
    document.querySelectorAll('.menu-item').forEach(function (item) {
        if (item.getAttribute('href') === pagina) {
            item.classList.add('active');
        }
    });

    // Inicializar funcionalidades
    inicializarFiltros();
    inicializarPaginacao();
    inicializarAcoes();
    inicializarMenuMobile();
});

// Inicializar filtros de tabela
function inicializarFiltros() {
    document.querySelectorAll('.filter-section').forEach(function (section) {
        const button = section.querySelector('button.btn-primary');
        if (!button) return;

        button.addEventListener('click', function (event) {
            event.preventDefault();
            aplicarFiltroTabela(section);
        });

        const inputs = section.querySelectorAll('input, select');
        inputs.forEach(function (input) {
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    aplicarFiltroTabela(section);
                }
            });
        });
    });
}

// Aplicar filtro na tabela
function aplicarFiltroTabela(section) {
    const searchInput = section.querySelector('input[type="text"], input[type="search"]');
    const searchText = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const selects = Array.from(section.querySelectorAll('select'));
    
    const filters = selects
        .map(function (select) {
            const value = select.value.trim().toLowerCase();
            const text = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text.toLowerCase() : '';
            return { value: value, text: text, element: select };
        })
        .filter(function (item) {
            return item.value !== '' && item.value !== 'todos os estados' && item.value !== 'selecione...';
        });

    const table = section.closest('.main-content')?.querySelector('table');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(function (row) {
        const rowText = row.innerText.toLowerCase();
        let visible = true;

        if (searchText && rowText.indexOf(searchText) === -1) {
            visible = false;
        }

        filters.forEach(function (filter) {
            if (visible && rowText.indexOf(filter.text) === -1) {
                visible = false;
            }
        });

        row.style.display = visible ? '' : 'none';
    });
}

// Inicializar paginação
function inicializarPaginacao() {
    document.querySelectorAll('.pagination').forEach(function (pagination) {
        pagination.querySelectorAll('.page-btn').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();

                if (btn.querySelector('i')) {
                    return;
                }

                pagination.querySelectorAll('.page-btn.active').forEach(function (activeBtn) {
                    activeBtn.classList.remove('active');
                });
                btn.classList.add('active');
            });
        });
    });
}

// Inicializar ações dos botões
function inicializarAcoes() {
    document.querySelectorAll('.action-btn').forEach(function (button) {
        if (button.hasAttribute('onclick')) {
            return;
        }

        button.addEventListener('click', function (event) {
            event.preventDefault();
            const text = button.innerText.toLowerCase();
            const iconEye = button.querySelector('.fa-eye');
            const iconEdit = button.querySelector('.fa-edit');
            const iconTag = button.querySelector('.fa-tag');

            if (iconEye || text.includes('ver')) {
                mostrarNotificacao('Visualizar detalhes ainda não está implementado.', 'info');
                return;
            }
            if (iconEdit || text.includes('editar') || text.includes('edit')) {
                mostrarNotificacao('Edição ainda não está implementada.', 'info');
                return;
            }
            if (iconTag) {
                mostrarNotificacao('Ação de etiqueta/tag ainda não está implementada.', 'info');
                return;
            }
            mostrarNotificacao('Ação do botão ainda não está implementada.', 'info');
        });
    });
}

// Inicializar menu mobile
function inicializarMenuMobile() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Fechar menu ao clicar em um item
        document.querySelectorAll('.menu-item').forEach(function (item) {
            item.addEventListener('click', function () {
                sidebar.classList.remove('open');
            });
        });
    }
}
