#!/usr/bin/env python3
"""
Teste de diagnóstico completo do sistema
"""

import requests
import json
import subprocess
import time
import sys
import os

def print_header(msg):
    print(f"\n{'='*60}")
    print(f"  {msg}")
    print(f"{'='*60}\n")

def test_servidor_rodando():
    """Verifica se o servidor Flask está rodando"""
    print_header("1. Testando se o servidor está rodando")
    
    try:
        response = requests.get('http://localhost:5000/api/healthcheck', timeout=5)
        print(f"✅ Servidor respondeu com status: {response.status_code}")
        print(f"Resposta: {response.json()}")
        return True
    except requests.exceptions.ConnectionError:
        print("❌ Não conseguiu conectar ao servidor em http://localhost:5000")
        print("   Verifique se o servidor está rodando: python app.py")
        return False
    except Exception as e:
        print(f"❌ Erro ao testar servidor: {e}")
        return False

def test_banco_dados():
    """Testa conexão com banco de dados diretamente"""
    print_header("2. Testando conexão com banco de dados")
    
    try:
        import mysql.connector
        
        conexao = mysql.connector.connect(
            host='200.131.251.11',
            port=3341,
            user='2026Iventario',
            password='Inventa@2026',
            database='2026ProjetoInv'
        )
        
        cursor = conexao.cursor()
        cursor.execute("SELECT COUNT(*) FROM usuario")
        resultado = cursor.fetchone()
        print(f"✅ Banco de dados conectado com sucesso!")
        print(f"   Total de usuários: {resultado[0]}")
        
        # Listar usuários
        cursor.execute("SELECT id, nome, email, tipoUsuarioId FROM usuario")
        usuarios = cursor.fetchall()
        print(f"\n   Usuários cadastrados:")
        for usuario in usuarios:
            print(f"   - {usuario[1]} ({usuario[2]}) - Tipo: {usuario[3]}")
        
        cursor.close()
        conexao.close()
        return True
    except Exception as e:
        print(f"❌ Erro ao conectar ao banco: {e}")
        return False

def test_login():
    """Testa o endpoint de login"""
    print_header("3. Testando endpoint de login")
    
    try:
        payload = {
            "email": "admin@gmail.com",
            "senha": "admin123"
        }
        
        print(f"Enviando: {json.dumps(payload, indent=2)}")
        
        response = requests.post(
            'http://localhost:5000/api/login_simple',
            json=payload,
            timeout=5
        )
        
        print(f"\nStatus da resposta: {response.status_code}")
        print(f"Headers: {dict(response.headers)}")
        
        try:
            resposta_json = response.json()
            print(f"Resposta JSON:\n{json.dumps(resposta_json, indent=2)}")
            
            if response.status_code == 200 and resposta_json.get('success'):
                print("\n✅ Login bem-sucedido!")
                return True
            else:
                print(f"\n⚠️  Login retornou erro: {resposta_json.get('message')}")
                return False
        except json.JSONDecodeError:
            print(f"❌ Servidor retornou resposta inválida (não é JSON)")
            print(f"Corpo da resposta: {response.text[:200]}")
            return False
            
    except requests.exceptions.ConnectionError:
        print("❌ Não conseguiu conectar ao servidor")
        return False
    except Exception as e:
        print(f"❌ Erro ao testar login: {e}")
        return False

def main():
    print("\n" + "="*60)
    print("  TESTE DE DIAGNÓSTICO - e-SGP Login")
    print("="*60)
    
    # Teste 1: Servidor rodando
    servidor_ok = test_servidor_rodando()
    
    # Teste 2: Banco de dados
    banco_ok = test_banco_dados()
    
    # Teste 3: Login (se os anteriores passaram)
    if servidor_ok and banco_ok:
        login_ok = test_login()
    else:
        print_header("3. Testando endpoint de login")
        print("⏭️  Pulando teste de login pois servidor ou banco não estão funcionando")
        login_ok = False
    
    # Resumo final
    print_header("RESUMO FINAL")
    
    print(f"✓ Servidor Flask:           {'✅ OK' if servidor_ok else '❌ ERRO'}")
    print(f"✓ Banco de Dados:           {'✅ OK' if banco_ok else '❌ ERRO'}")
    print(f"✓ Endpoint de Login:        {'✅ OK' if login_ok else '❌ ERRO'}")
    
    if servidor_ok and banco_ok and login_ok:
        print("\n🎉 TUDO FUNCIONANDO CORRETAMENTE!")
        return 0
    else:
        print("\n⚠️  Há alguns problemas a resolver.")
        return 1

if __name__ == '__main__':
    try:
        sys.exit(main())
    except KeyboardInterrupt:
        print("\n\nTeste cancelado pelo usuário.")
        sys.exit(1)
