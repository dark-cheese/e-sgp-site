#!/bin/bash

echo "============================================"
echo "  SCRIPT DE SETUP FINAL - e-SGP"
echo "============================================"
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função de status
status_ok() {
    echo -e "${GREEN}✅ $1${NC}"
}

status_error() {
    echo -e "${RED}❌ $1${NC}"
}

status_info() {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

echo ""
echo "📋 VERIFICANDO ARQUIVOS..."
echo ""

# Verificar arquivos críticos
declare  -a files=(
    "app.py"
    "requirements.txt"
    "Procfile"
    "render.yaml"
    ".env.example"
    ".gitignore"
    "index.html"
    "auth.js"
    "main.js"
    "utils.js"
    "DEPLOY_RENDER.md"
    "SETUP_LOGIN.md"
)

all_good=true

for file in "${files[@]}"; do
    if [ -f "$file" ] || [ -d "$file" ]; then
        status_ok "Arquivo encontrado: $file"
    else
        status_error "Arquivo faltando: $file"
        all_good=false
    fi
done

echo ""
echo "📊 VERIFICANDO DEPENDÊNCIAS PYTHON..."
echo ""

if grep -q "Flask" requirements.txt && \
   grep -q "SQLAlchemy" requirements.txt && \
   grep -q "flask-cors" requirements.txt && \
   grep -q "mysql-connector-python" requirements.txt && \
   grep -q "gunicorn" requirements.txt; then
    status_ok "Todas as dependências configuradas"
else
    status_error "Faltam dependências em requirements.txt"
    all_good=false
fi

echo ""
echo "🌐 VERIFICANDO PROCFILE..."
echo ""

if grep -q "gunicorn" Procfile; then
    status_ok "Procfile configurado para Render"
else
    status_error "Procfile não aponta para gunicorn"
    all_good=false
fi

echo ""
echo "📝 VERIFICANDO RENDER.YAML..."
echo ""

if [ -f "render.yaml" ]; then
    if grep -q "web:" render.yaml && grep -q "gunicorn" render.yaml; then
        status_ok "render.yaml configurado corretamente"
    else
        status_error "render.yaml pode estar incompleto"
        all_good=false
    fi
fi

echo ""
echo "============================================"
if [ "$all_good" = true ]; then
    echo -e "${GREEN}✅ TUDO PRONTO PARA DEPLOY NO RENDER!${NC}"
    echo "============================================"
    echo ""
    echo "Próximas etapas:"
    echo ""
    echo "1️⃣  Fazer commit e push para GitHub:"
    echo "   git add ."
    echo "   git commit -m 'Preparar para deploy Render'"
    echo "   git push origin main"
    echo ""
    echo "2️⃣  Acessar https://render.com"
    echo ""
    echo "3️⃣  Conectar seu repositório GitHub"
    echo ""
    echo "4️⃣  Configurar Environment Variables:"
    echo "   - DATABASE_URL (ou variáveis individuais)"
    echo "   - FLASK_ENV=production"
    echo ""
    echo "5️⃣  Deploy automático!"
    echo ""
    echo "📖 Leia: DEPLOY_RENDER.md para instruções detalhadas"
    echo ""
else
    echo -e "${RED}⚠️  VERIFIQUE OS ERROS ACIMA ANTES DE FAZER DEPLOY${NC}"
    echo "============================================"
    exit 1
fi
