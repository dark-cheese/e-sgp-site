#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
from config import DB_CONFIG

try:
    import mysql.connector
    print("✓ mysql-connector-python está instalado")
except ImportError as e:
    print(f"✗ Erro ao importar mysql-connector-python: {e}")
    print("\nInstale com: pip install mysql-connector-python")
    sys.exit(1)

print("\n" + "="*60)
print("TESTANDO CONEXÃO COM O BANCO DE DADOS")
print("="*60)
print(f"\nConfigurações:")
print(f"  Host: {DB_CONFIG['host']}")
print(f"  Port: {DB_CONFIG['port']}")
print(f"  Database: {DB_CONFIG['database']}")
print(f"  User: {DB_CONFIG['user']}")
print()

try:
    conn = mysql.connector.connect(
        host=DB_CONFIG['host'],
        port=DB_CONFIG['port'],
        user=DB_CONFIG['user'],
        password=DB_CONFIG['password'],
        database=DB_CONFIG['database'],
        charset=DB_CONFIG['charset'],
        use_unicode=DB_CONFIG['use_unicode'],
    )
    print("✓ Conexão estabelecida com sucesso!")
    
    cursor = conn.cursor(dictionary=True)
    
    # Test 1: Check if tables exist
    cursor.execute("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s LIMIT 5", 
                   (DB_CONFIG['database'],))
    tables = cursor.fetchall()
    print(f"\n✓ Tabelas encontradas ({len(tables)} primeiras):")
    for table in tables:
        print(f"  - {table['TABLE_NAME']}")
    
    # Test 2: Try a simple query
    cursor.execute("SELECT COUNT(*) as total FROM secretaria")
    result = cursor.fetchone()
    print(f"\n✓ Total de secretarias: {result['total']}")
    
    # Test 3: Check user table
    cursor.execute("SELECT COUNT(*) as total FROM usuario")
    result = cursor.fetchone()
    print(f"✓ Total de usuários: {result['total']}")
    
    cursor.close()
    conn.close()
    
    print("\n" + "="*60)
    print("✓ TODAS AS VERIFICAÇÕES PASSARAM - BANCO CONECTADO!")
    print("="*60)

except mysql.connector.Error as err:
    print(f"\n✗ ERRO DE CONEXÃO: {err.msg}")
    print(f"  Código: {err.errno}")
    
    if err.errno == 2003:
        print("  → Problema: Não conseguiu conectar ao servidor")
    elif err.errno == 1045:
        print("  → Problema: Credenciais inválidas (usuário/senha)")
    elif err.errno == 1049:
        print("  → Problema: Banco de dados não existe")
    
    sys.exit(1)

except Exception as e:
    print(f"\n✗ ERRO INESPERADO: {e}")
    sys.exit(1)
