#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Script de teste para o sistema de autenticação
Testará os endpoints de login, logout e healthcheck
"""

import requests
import json
import sys

BASE_URL = "http://localhost:5000"

def print_resultado(teste, sucesso, mensagem=""):
    status = "✓" if sucesso else "✗"
    print(f"{status} {teste}")
    if mensagem:
        print(f"  └─ {mensagem}")

def teste_healthcheck():
    """Teste 1: Verificar saúde da aplicação"""
    try:
        response = requests.get(f"{BASE_URL}/api/healthcheck", timeout=5)
        dados = response.json()
        
        if response.status_code == 200:
            print_resultado(
                "HEALTHCHECK",
                True,
                f"Server: {dados.get('server')}, Database: {dados.get('database')}"
            )
            return True
        else:
            print_resultado("HEALTHCHECK", False, f"Status: {response.status_code}")
            return False
    except requests.exceptions.ConnectionError:
        print_resultado("HEALTHCHECK", False, "Não conseguiu conectar ao servidor")
        return False
    except Exception as e:
        print_resultado("HEALTHCHECK", False, str(e))
        return False

def teste_login_valido():
    """Teste 2: Login com credenciais válidas"""
    try:
        payload = {
            "email": "admin@example.com",
            "senha": "admin123"
        }
        
        response = requests.post(
            f"{BASE_URL}/api/login_simple",
            json=payload,
            headers={"Content-Type": "application/json"},
            timeout=5
        )
        
        dados = response.json()
        
        if response.status_code == 200 and dados.get('success'):
            usuario = dados.get('usuario', {})
            print_resultado(
                "LOGIN VÁLIDO",
                True,
                f"User: {usuario.get('email')} ({usuario.get('id')})"
            )
            return True, dados
        else:
            print_resultado(
                "LOGIN VÁLIDO",
                False,
                f"Status: {response.status_code}, Msg: {dados.get('message')}"
            )
            return False, None
    except Exception as e:
        print_resultado("LOGIN VÁLIDO", False, str(e))
        return False, None

def teste_login_invalido():
    """Teste 3: Login com credenciais inválidas"""
    try:
        payload = {
            "email": "invalido@example.com",
            "senha": "senhaerrada"
        }
        
        response = requests.post(
            f"{BASE_URL}/api/login_simple",
            json=payload,
            headers={"Content-Type": "application/json"},
            timeout=5
        )
        
        dados = response.json()
        
        if response.status_code == 401 and not dados.get('success'):
            print_resultado(
                "LOGIN INVÁLIDO",
                True,
                "Corretamente rejeitado"
            )
            return True
        else:
            print_resultado(
                "LOGIN INVÁLIDO",
                False,
                f"Deveria ter sido rejeitado, mas retornou: {response.status_code}"
            )
            return False
    except Exception as e:
        print_resultado("LOGIN INVÁLIDO", False, str(e))
        return False

def teste_login_campos_vazios():
    """Teste 4: Login com campos vazios"""
    try:
        payload = {
            "email": "",
            "senha": ""
        }
        
        response = requests.post(
            f"{BASE_URL}/api/login_simple",
            json=payload,
            headers={"Content-Type": "application/json"},
            timeout=5
        )
        
        dados = response.json()
        
        if response.status_code == 400:
            print_resultado(
                "CAMPOS VAZIOS",
                True,
                "Corretamente rejeitado"
            )
            return True
        else:
            print_resultado(
                "CAMPOS VAZIOS",
                False,
                f"Deveria retornar 400, mas retornou: {response.status_code}"
            )
            return False
    except Exception as e:
        print_resultado("CAMPOS VAZIOS", False, str(e))
        return False

def teste_logout():
    """Teste 5: Endpoint de logout"""
    try:
        response = requests.post(
            f"{BASE_URL}/api/logout",
            timeout=5
        )
        
        dados = response.json()
        
        if response.status_code == 200 and dados.get('success'):
            print_resultado(
                "LOGOUT",
                True,
                "Endpoint funcionando"
            )
            return True
        else:
            print_resultado("LOGOUT", False, f"Status: {response.status_code}")
            return False
    except Exception as e:
        print_resultado("LOGOUT", False, str(e))
        return False

def main():
    print("\n" + "="*60)
    print("TESTES DO SISTEMA DE AUTENTICAÇÃO - e-SGP")
    print("="*60 + "\n")
    
    # Teste 1: Healthcheck
    healthcheck_ok = teste_healthcheck()
    print()
    
    if not healthcheck_ok:
        print("❌ Servidor não está respondendo. Verifique se está rodando:")
        print("   python app.py")
        sys.exit(1)
    
    # Teste 2: Login válido
    login_ok, dados_login = teste_login_valido()
    print()
    
    # Teste 3: Login inválido
    teste_login_invalido()
    print()
    
    # Teste 4: Campos vazios
    teste_login_campos_vazios()
    print()
    
    # Teste 5: Logout
    teste_logout()
    print()
    
    # Resumo
    print("="*60)
    if login_ok and healthcheck_ok:
        print("✓ TODOS OS TESTES PASSARAM!")
        print("✓ Sistema de autenticação está funcionando corretamente!")
    else:
        print("✗ Alguns testes falharam. Verifique os erros acima.")
        sys.exit(1)
    print("="*60 + "\n")

if __name__ == "__main__":
    main()
