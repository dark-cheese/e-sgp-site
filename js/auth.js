// js/auth.js
// Funções de autenticação

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

async function login(event) {
    event.preventDefault();
    
    const email = document.getElementById('email')?.value;
    const senha = document.getElementById('password')?.value;
    
    if (!email || !senha) {
        alert('Preencha todos os campos!');
        return;
    }
    
    // Caminho CORRETO para sua estrutura
    const url = '/sql/e-sgp-site/backend/api/login_simple.php';
    
    console.log('Tentando login em:', url); // Para debug
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, senha })
        });
        
        console.log('Status da resposta:', response.status); // Para debug
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Resposta:', data); // Para debug
        
        if (data.success) {
            alert('Login realizado com sucesso!');
            sessionStorage.setItem('usuario', JSON.stringify(data.usuario));
            window.location.href = '/sql/e-sgp-site/frontend/dashboard.html';
        } else {
            alert(data.message || 'Erro no login!');
        }
    } catch (error) {
        console.error('Erro detalhado:', error);
        alert('Erro ao conectar com o servidor!\n\n' + error.message + '\n\nVerifique se o arquivo login_simple.php existe em:\n/sql/e-sgp-site/backend/api/');
    }
}

// Configurar o formulário quando a página carregar
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', login);
}