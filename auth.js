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
    
    // CORREÇÃO: Adicionada a barra '/' no início para garantir a rota absoluta na raiz do Render
    const url = '/api/login_simple';
    console.log('Tentando login em:', url);
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, senha })
        });
        
        // Melhoria: Se a resposta não for 200 OK, tenta ler o erro enviado pelo Flask
        if (!response.ok) {
            let errorMessage = `HTTP error! status: ${response.status}`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // Se não for um JSON (ex: erro 500 do Render), lê como texto puro
                const errorText = await response.text();
                console.error('Resposta do servidor não é JSON:', errorText);
            }
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        
        if (data.success) {
            alert('Login realizado com sucesso!');
            sessionStorage.setItem('usuario', JSON.stringify(data.usuario));
            window.location.href = 'dashboard.html';
        } else {
            alert(data.message || 'Erro no login!');
        }
    } catch (error) {
        console.error('Erro detalhado:', error);
        alert('Erro ao conectar com o servidor!\n\n' + error.message);
    }
}

// Configurar o formulário quando a página carregar
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', login);
}
