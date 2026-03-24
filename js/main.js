// ===== FUNÇÕES GERAIS =====

function mostrarData() {
    var data = new Date();
    var dia = String(data.getDate()).padStart(2, '0');
    var mes = String(data.getMonth() + 1).padStart(2, '0');
    var ano = data.getFullYear();
    return dia + '/' + mes + '/' + ano;
}

function saudacao() {
    var hora = new Date().getHours();
    if (hora < 12) return 'Bom dia';
    if (hora < 18) return 'Boa tarde';
    return 'Boa noite';
}

document.addEventListener('DOMContentLoaded', function () {

    // Atualiza data no header
    var dataEl = document.querySelector('.date-display');
    if (dataEl) {
        dataEl.innerHTML = '<i class="far fa-calendar-alt"></i> ' + mostrarData();
    }

    // Saudação no welcome card
    var saudacaoEl = document.querySelector('.welcome-card h2');
    if (saudacaoEl) {
        saudacaoEl.textContent = saudacao() + ', Admin!';
    }

    // Marca menu ativo automaticamente
    var pagina = window.location.pathname.split('/').pop() || 'dashboard.html';
    document.querySelectorAll('.menu-item').forEach(function (item) {
        if (item.getAttribute('href') === pagina) {
            item.classList.add('active');
        }
    });

});
