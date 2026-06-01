#!/usr/bin/env python3
"""
Verificador de Prontidão para Deploy no Render
"""

import os
import sys
import json

def check_file(filepath, description):
    """Verifica se um arquivo existe"""
    if os.path.exists(filepath):
        print(f"✅ {description}: {filepath}")
        return True
    else:
        print(f"❌ {description}: FALTANDO ({filepath})")
        return False

def check_requirements():
    """Verifica se requirements.txt contém as dependências necessárias"""
    required = [
        'Flask',
        'Flask-SQLAlchemy',
        'flask-cors',
        'mysql-connector-python',
        'gunicorn'
    ]
    
    if not os.path.exists('requirements.txt'):
        print("❌ requirements.txt: NÃO ENCONTRADO")
        return False
    
    with open('requirements.txt', 'r') as f:
        content = f.read()
    
    missing = []
    for req in required:
        if req not in content:
            missing.append(req)
    
    if missing:
        print(f"❌ requirements.txt: Faltam dependências: {', '.join(missing)}")
        return False
    else:
        print(f"✅ requirements.txt: COMPLETO")
        return True

def check_procfile():
    """Verifica Procfile"""
    if not os.path.exists('Procfile'):
        print("❌ Procfile: NÃO ENCONTRADO")
        return False
    
    with open('Procfile', 'r') as f:
        content = f.read().strip()
    
    if 'gunicorn' in content:
        print(f"✅ Procfile: CORRETO")
        return True
    else:
        print(f"❌ Procfile: Comando inválido")
        return False

def check_app_py():
    """Verifica se app.py usa variáveis de ambiente"""
    if not os.path.exists('app.py'):
        print("❌ app.py: NÃO ENCONTRADO")
        return False
    
    with open('app.py', 'r') as f:
        content = f.read()
    
    checks = [
        ('DATABASE_URL', 'Suporta DATABASE_URL'),
        ('os.environ', 'Usa variáveis de ambiente'),
        ('gunicorn', 'Compatível com gunicorn') or True,
    ]
    
    missing = []
    for check, desc in checks:
        if check not in content:
            missing.append(desc)
    
    if missing:
        print(f"⚠️  app.py: Possíveis problemas: {', '.join(missing)}")
        return False
    else:
        print(f"✅ app.py: CONFIGURADO PARA RENDER")
        return True

def check_env_example():
    """Verifica .env.example"""
    if not os.path.exists('.env.example'):
        print("❌ .env.example: NÃO ENCONTRADO")
        return False
    else:
        print(f"✅ .env.example: PRESENTE")
        return True

def check_gitignore():
    """Verifica .gitignore"""
    if not os.path.exists('.gitignore'):
        print("⚠️  .gitignore: NÃO ENCONTRADO (recomendado criar)")
        return False
    else:
        print(f"✅ .gitignore: PRESENTE")
        return True

def check_render_yaml():
    """Verifica render.yaml"""
    if not os.path.exists('render.yaml'):
        print("⚠️  render.yaml: NÃO ENCONTRADO (opcional, mas recomendado)")
        return False
    else:
        print(f"✅ render.yaml: PRESENTE")
        return True

def check_static_files():
    """Verifica se arquivos estáticos estão presentes"""
    required_files = [
        ('index.html', 'Página de login'),
        ('auth.js', 'Script de autenticação'),
        ('main.js', 'Script principal'),
        ('utils.js', 'Utilitários'),
    ]
    
    all_exist = True
    for filepath, desc in required_files:
        if check_file(filepath, desc):
            pass
        else:
            all_exist = False
    
    return all_exist

def main():
    print("\n" + "="*60)
    print("  VERIFICADOR DE PRONTIDÃO - DEPLOY RENDER")
    print("="*60 + "\n")
    
    checks = [
        ("Arquivos de Configuração", [
            check_procfile,
            check_app_py,
            check_env_example,
            check_gitignore,
            check_render_yaml,
        ]),
        ("Dependências Python", [
            check_requirements,
        ]),
        ("Arquivos Estáticos", [
            check_static_files,
        ]),
    ]
    
    results = {}
    
    for section, check_funcs in checks:
        print(f"\n📋 {section}")
        print("-" * 60)
        section_ok = True
        for check_func in check_funcs:
            if not check_func():
                section_ok = False
        results[section] = section_ok
    
    # Resumo
    print("\n" + "="*60)
    print("  RESUMO FINAL")
    print("="*60 + "\n")
    
    all_ok = True
    for section, ok in results.items():
        status = "✅" if ok else "❌"
        print(f"{status} {section}")
        if not ok:
            all_ok = False
    
    print("\n" + "="*60)
    
    if all_ok:
        print("🎉 PRONTO PARA DEPLOY NO RENDER!")
        print("\nPróximos passos:")
        print("1. Push para GitHub: git push origin main")
        print("2. Acesse render.com e conecte o repositório")
        print("3. Configure as Environment Variables")
        print("4. Deploy!")
        return 0
    else:
        print("⚠️  VERIFIQUE OS ERROS ACIMA ANTES DE FAZER DEPLOY")
        return 1

if __name__ == '__main__':
    sys.exit(main())
