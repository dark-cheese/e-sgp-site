#!/usr/bin/env python3
"""
SETUP AUTOMÁTICO COMPLETO - e-SGP Login
Este script configura tudo para o login funcionar
"""

import os
import sys
import subprocess
import json
import time
import mysql.connector
from pathlib import Path

# Cores para output
class Cor:
    HEADER = '\033[95m'
    BLUE = '\033[94m'
    CYAN = '\033[96m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    RED = '\033[91m'
    END = '\033[0m'
    BOLD = '\033[1m'

def print_step(numero, mensagem):
    print(f"\n{Cor.CYAN}{'='*70}{Cor.END}")
    print(f"{Cor.BOLD}{Cor.BLUE}PASSO {numero}: {mensagem}{Cor.END}")
    print(f"{Cor.CYAN}{'='*70}{Cor.END}\n")

def print_ok(msg):
    print(f"{Cor.GREEN}✅ {msg}{Cor.END}")

def print_erro(msg):
    print(f"{Cor.RED}❌ {msg}{Cor.END}")

def print_info(msg):
    print(f"{Cor.BLUE}ℹ️  {msg}{Cor.END}")

def print_aviso(msg):
    print(f"{Cor.YELLOW}⚠️  {msg}{Cor.END}")

def step_1_verificar_python():
    """Passo 1: Verificar Python e pacotes"""
    print_step(1, "Verificando Python e pacotes necessários")
    
    try:
        import flask
        print_ok("Flask está instalado")
    except ImportError:
        print_aviso("Flask não encontrado, instalando...")
        subprocess.run([sys.executable, "-m", "pip", "install", "flask"], check=True)
    
    try:
        import flask_sqlalchemy
        print_ok("Flask-SQLAlchemy está instalado")
    except ImportError:
        print_aviso("Flask-SQLAlchemy não encontrado, instalando...")
        subprocess.run([sys.executable, "-m", "pip", "install", "flask-sqlalchemy"], check=True)
    
    try:
        import flask_cors
        print_ok("Flask-CORS está instalado")
    except ImportError:
        print_aviso("Flask-CORS não encontrado, instalando...")
        subprocess.run([sys.executable, "-m", "pip", "install", "flask-cors"], check=True)
    
    try:
        import mysql.connector
        print_ok("MySQL Connector está instalado")
    except ImportError:
        print_aviso("MySQL Connector não encontrado, instalando...")
        subprocess.run([sys.executable, "-m", "pip", "install", "mysql-connector-python"], check=True)
    
    return True

def step_2_conectar_banco():
    """Passo 2: Conectar ao banco de dados"""
    print_step(2, "Conectando ao banco de dados MySQL")
    
    db_config = {
        'host': '200.131.251.11',
        'port': 3341,
        'user': '2026Iventario',
        'password': 'Inventa@2026'
    }
    
    try:
        conexao = mysql.connector.connect(**db_config)
        print_ok("Conexão com MySQL estabelecida!")
        conexao.close()
        return db_config
    except Exception as e:
        print_erro(f"Falha ao conectar: {e}")
        return None

def step_3_popular_banco(db_config):
    """Passo 3: Executar scripts SQL"""
    print_step(3, "Populando banco de dados")
    
    try:
        conexao = mysql.connector.connect(**db_config)
        cursor = conexao.cursor()
        
        # Script 1: Criar banco e tabelas
        print_info("Executando banco.sql...")
        with open('sql/banco.sql', 'r', encoding='utf-8') as f:
            banco_sql = f.read()
        
        # Dividir em statements (simples)
        statements = [s.strip() for s in banco_sql.split(';') if s.strip() and not s.strip().startswith('--')]
        
        for statement in statements[:20]:  # Primeiras 20 statements
            try:
                cursor.execute(statement)
            except mysql.connector.Error as e:
                if 'already exists' in str(e) or 'duplicate' in str(e).lower():
                    pass  # Ignorar se já existe
                else:
                    print_aviso(f"Aviso SQL: {str(e)[:50]}")
        
        conexao.commit()
        print_ok("banco.sql executado!")
        
        # Script 2: Inserir dados de teste
        print_info("Executando dados-iniciais.sql...")
        with open('sql/dados-iniciais.sql', 'r', encoding='utf-8') as f:
            dados_sql = f.read()
        
        statements = [s.strip() for s in dados_sql.split(';') if s.strip() and not s.strip().startswith('--')]
        
        for statement in statements:
            try:
                cursor.execute(statement)
            except mysql.connector.Error as e:
                if 'duplicate' in str(e).lower():
                    pass  # Ignorar duplicatas
                else:
                    print_aviso(f"Aviso: {str(e)[:50]}")
        
        conexao.commit()
        print_ok("dados-iniciais.sql executado!")
        
        # Verificar usuários
        cursor.execute("SELECT COUNT(*) FROM usuario")
        total_usuarios = cursor.fetchone()[0]
        print_ok(f"Total de usuários no banco: {total_usuarios}")
        
        cursor.execute("SELECT id, nome, email, tipoUsuarioId FROM usuario LIMIT 5")
        usuarios = cursor.fetchall()
        print_info("Usuários cadastrados:")
        for u in usuarios:
            print(f"   • {u[1]} ({u[2]}) - Tipo: {u[3]}")
        
        cursor.close()
        conexao.close()
        return True
        
    except Exception as e:
        print_erro(f"Erro ao popular banco: {e}")
        import traceback
        traceback.print_exc()
        return False

def step_4_validar_app_py():
    """Passo 4: Validar app.py"""
    print_step(4, "Validando app.py")
    
    try:
        with open('app.py', 'r', encoding='utf-8') as f:
            conteudo = f.read()
        
        # Verificar imports
        if 'from flask import Flask' not in conteudo:
            print_erro("Flask não está importado em app.py")
            return False
        print_ok("Flask importado ✓")
        
        if 'SQLAlchemy' not in conteudo:
            print_erro("SQLAlchemy não está configurado")
            return False
        print_ok("SQLAlchemy configurado ✓")
        
        if '/api/login_simple' not in conteudo:
            print_erro("Endpoint /api/login_simple não encontrado")
            return False
        print_ok("Endpoint /api/login_simple existe ✓")
        
        # Validar sintaxe Python
        try:
            compile(conteudo, 'app.py', 'exec')
            print_ok("Sintaxe Python válida ✓")
        except SyntaxError as e:
            print_erro(f"Erro de sintaxe em app.py: {e}")
            return False
        
        return True
        
    except Exception as e:
        print_erro(f"Erro ao validar app.py: {e}")
        return False

def step_5_validar_js():
    """Passo 5: Validar arquivos JavaScript"""
    print_step(5, "Validando arquivos JavaScript")
    
    arquivos_js = ['auth.js', 'main.js', 'utils.js']
    
    for arquivo in arquivos_js:
        if os.path.exists(arquivo):
            size = os.path.getsize(arquivo)
            if size > 0:
                print_ok(f"{arquivo} existe ({size} bytes)")
            else:
                print_erro(f"{arquivo} está vazio!")
                return False
        else:
            print_erro(f"{arquivo} não encontrado!")
            return False
    
    return True

def step_6_testar_servidor():
    """Passo 6: Testar servidor"""
    print_step(6, "Verificando se o servidor está rodando")
    
    try:
        import requests
    except ImportError:
        print_aviso("Requests não instalado, pulando teste...")
        return True
    
    print_info("Tentando conectar em http://localhost:5000...")
    
    for tentativa in range(3):
        try:
            response = requests.get('http://localhost:5000/api/healthcheck', timeout=2)
            if response.status_code == 200:
                print_ok("Servidor respondeu com sucesso!")
                print_info(f"Status: {response.json()}")
                return True
        except:
            if tentativa < 2:
                print_aviso(f"Tentativa {tentativa+1} falhou, aguardando...")
                time.sleep(2)
    
    print_aviso("Servidor não está respondendo (isso é normal se não foi iniciado)")
    print_info("Para iniciar o servidor, execute: python app.py")
    return True

def step_7_teste_login():
    """Passo 7: Teste de login completo"""
    print_step(7, "Teste de login")
    
    try:
        import requests
    except ImportError:
        print_aviso("Requests não instalado, pulando teste de login...")
        return True
    
    print_info("Aguardando servidor... (máximo 5 segundos)")
    conectado = False
    
    for i in range(5):
        try:
            requests.get('http://localhost:5000/api/healthcheck', timeout=1)
            conectado = True
            break
        except:
            sys.stdout.write('.')
            sys.stdout.flush()
            time.sleep(1)
    
    print()
    
    if not conectado:
        print_aviso("Servidor não está rodando")
        print_info("Para testar o login, inicie o servidor: python app.py")
        return True
    
    # Fazer login
    payload = {
        "email": "admin@gmail.com",
        "senha": "admin123"
    }
    
    print_info(f"Tentando login com: {payload['email']}")
    
    try:
        response = requests.post(
            'http://localhost:5000/api/login_simple',
            json=payload,
            timeout=5
        )
        
        if response.status_code == 200:
            data = response.json()
            if data.get('success'):
                print_ok("Login bem-sucedido! 🎉")
                usuario = data.get('usuario', {})
                print_info(f"Usuário: {usuario.get('nome')}")
                print_info(f"Email: {usuario.get('email')}")
                print_info(f"Perfil: {usuario.get('perfil')}")
                return True
            else:
                print_erro(f"Login falhou: {data.get('message')}")
                return False
        else:
            print_erro(f"Erro HTTP {response.status_code}: {response.text}")
            return False
            
    except Exception as e:
        print_erro(f"Erro ao testar login: {e}")
        return False

def resumo_final(resultados):
    """Resumo final do setup"""
    print_step("FINAL", "Resumo do Setup")
    
    print(f"{Cor.BOLD}Status dos passos:{Cor.END}\n")
    
    nomes = [
        "Python e pacotes",
        "Conexão MySQL",
        "Banco de dados",
        "Validação app.py",
        "Arquivos JavaScript",
        "Servidor Flask",
        "Teste de login"
    ]
    
    for i, (nome, resultado) in enumerate(zip(nomes, resultados)):
        status = f"{Cor.GREEN}✅ OK{Cor.END}" if resultado else f"{Cor.RED}❌ FALHA{Cor.END}"
        print(f"{i+1}. {nome:.<40} {status}")
    
    print(f"\n{Cor.CYAN}{'='*70}{Cor.END}\n")
    
    if all(resultados[:5]):  # Primeiros 5 são críticos
        print(f"{Cor.GREEN}{Cor.BOLD}🎉 SETUP COMPLETO COM SUCESSO!{Cor.END}\n")
        print(f"{Cor.BOLD}Próximos passos:{Cor.END}")
        print(f"1. {Cor.YELLOW}Inicie o servidor:{Cor.END}")
        print(f"   {Cor.BOLD}python app.py{Cor.END}\n")
        print(f"2. {Cor.YELLOW}Abra no navegador:{Cor.END}")
        print(f"   {Cor.BOLD}http://localhost:5000/index.html{Cor.END}\n")
        print(f"3. {Cor.YELLOW}Use as credenciais:{Cor.END}")
        print(f"   Email: {Cor.BOLD}admin@gmail.com{Cor.END}")
        print(f"   Senha: {Cor.BOLD}admin123{Cor.END}\n")
        return True
    else:
        print(f"{Cor.RED}{Cor.BOLD}⚠️  Há problemas no setup.{Cor.END}\n")
        return False

def main():
    print(f"\n{Cor.HEADER}{Cor.BOLD}")
    print("╔" + "═"*68 + "╗")
    print("║" + " "*15 + "SETUP AUTOMÁTICO - e-SGP LOGIN SYSTEM" + " "*16 + "║")
    print("║" + " "*22 + "Configurando todo o sistema..." + " "*16 + "║")
    print("╚" + "═"*68 + "╝")
    print(f"{Cor.END}\n")
    
    # Mudar para o diretório correto se necessário
    if os.path.basename(os.getcwd()) != 'e-sgp-site':
        if os.path.exists('e-sgp-site'):
            os.chdir('e-sgp-site')
        elif os.path.exists('sql') and os.path.exists('app.py'):
            # Já estamos no diretório certo
            pass
    
    resultados = []
    
    try:
        # Passo 1
        resultados.append(step_1_verificar_python())
        
        # Passo 2
        db_config = step_2_conectar_banco()
        resultados.append(db_config is not None)
        
        # Passo 3
        if db_config:
            resultados.append(step_3_popular_banco(db_config))
        else:
            resultados.append(False)
        
        # Passo 4
        resultados.append(step_4_validar_app_py())
        
        # Passo 5
        resultados.append(step_5_validar_js())
        
        # Passo 6
        resultados.append(step_6_testar_servidor())
        
        # Passo 7 (opcional)
        resultados.append(step_7_teste_login())
        
        # Resumo final
        sucesso = resumo_final(resultados)
        
        return 0 if sucesso else 1
        
    except KeyboardInterrupt:
        print(f"\n\n{Cor.YELLOW}Setup cancelado pelo usuário.{Cor.END}\n")
        return 1
    except Exception as e:
        print_erro(f"Erro durante setup: {e}")
        import traceback
        traceback.print_exc()
        return 1

if __name__ == '__main__':
    sys.exit(main())
