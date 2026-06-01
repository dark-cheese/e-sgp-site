// ===== SISTEMA DE AUTENTICAÇÃO =====

// Função para alternar visibilidade da senha
function toggleSenha() {
    const senha = document.getElementById('password');
    const icone = document.querySelector('.toggle-password');
    
    if (!senha || !icone) return;
    
    if (senha.type === 'password') {
        senha.type = 'text';
        icone.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        senha.type = 'password';
        icone.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Verificar se o usuário está logado
function verificarLogin() {
    const usuario = sessionStorage.getItem('usuario');
    if (!usuario && window.location.pathname.includes('index.html')) {
        // Está na página de login, permitir
        return;
    } else if (!usuario && !window.location.pathname.includes('index.html')) {
        // Não está na página de login e não tem sessão, redirecionar
        window.location.href = 'index.html';
    } else if (usuario && window.location.pathname.includes('index.html')) {
        // Está logado e na página de login, redirecionar ao dashboard
        window.location.href = 'dashboard.html';
    }
}

// Função de login
async function login(event) {
    event.preventDefault();
    
    const email = document.getElementById('email')?.value;
    const senha = document.getElementById('password')?.value;
    const botaoLogin = document.querySelector('.btn-login');
    
    if (!email || !senha) {
        mostrarNotificacao('Preencha todos os campos!', 'erro');
        return;
    }
    
    // Desabilitar botão durante requisição
    botaoLogin.disabled = true;
    botaoLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Autenticando...';
    
    try {
        const url = '/api/login_simple';
        console.log('Tentando login em:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, senha })
        });
        
        if (!response.ok) {
            let errorMessage = `Erro HTTP: ${response.status}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                const errorText = await response.text();
                console.error('Resposta não JSON:', errorText);
            }
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Armazenar dados do usuário
            sessionStorage.setItem('usuario', JSON.stringify({
                id: data.usuario?.id,
                email: data.usuario?.email,
                nome: data.usuario?.nome,
                perfil: data.usuario?.perfil
            }));
            
            mostrarNotificacao('Login realizado com sucesso!', 'sucesso');
            
            // Redirecionar após 800ms
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 800);
        } else {
            mostrarNotificacao(data.message || 'Erro ao realizar login!', 'erro');
            botaoLogin.disabled = false;
            botaoLogin.innerHTML = '<i class="fas fa-sign-in-alt"></i> Entrar';
        }
    } catch (error) {
        console.error('Erro detalhado:', error);
        mostrarNotificacao('Erro ao conectar com o servidor: ' + error.message, 'erro');
        botaoLogin.disabled = false;
        botaoLogin.innerHTML = '<i class="fas fa-sign-in-alt"></i> Entrar';
    }
}

// Função de logout
function logout() {
    sessionStorage.removeItem('usuario');
    window.location.href = 'index.html';
}

// Configurar o formulário quando a página carregar
document.addEventListener('DOMContentLoaded', function () {
    // Verificar autenticação
    verificarLogin();
    
    // Configurar evento de submit do formulário
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', login);
        
        // Focar no campo de email ao carregar
        document.getElementById('email').focus();
    }
});
