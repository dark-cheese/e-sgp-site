#!/bin/bash

# SCRIPT DE SETUP AUTOMÁTICO - e-SGP Login
# Este script configura e testa todo o sistema

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                  SETUP AUTOMÁTICO - e-SGP LOGIN                ║"
echo "║                 Configurando todo o sistema...                 ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "app.py" ]; then
    echo -e "${RED}❌ Erro: app.py não encontrado!${NC}"
    echo "Este script deve ser executado no diretório raiz do projeto"
    exit 1
fi

echo -e "${BLUE}[1/4] Instalando dependências Python...${NC}"
pip install -q -r requirements.txt
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Dependências instaladas${NC}"
else
    echo -e "${RED}❌ Erro ao instalar dependências${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[2/4] Conectando ao banco de dados e populando...${NC}"
python3 setup.py
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Banco de dados configurado${NC}"
else
    echo -e "${RED}❌ Erro ao configurar banco de dados${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[3/4] Verificando arquivos JavaScript...${NC}"
if [ -f "auth.js" ] && [ -f "main.js" ] && [ -f "utils.js" ]; then
    echo -e "${GREEN}✅ Todos os arquivos JavaScript encontrados${NC}"
else
    echo -e "${RED}❌ Arquivos JavaScript faltando${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[4/4] Resumo do Setup${NC}"
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ SETUP CONCLUÍDO COM SUCESSO!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo -e "${YELLOW}Próximos passos:${NC}"
echo ""
echo -e "${BLUE}1. Inicie o servidor Flask:${NC}"
echo -e "   ${GREEN}python app.py${NC}"
echo ""
echo -e "${BLUE}2. Abra o navegador em:${NC}"
echo -e "   ${GREEN}http://localhost:5000/index.html${NC}"
echo ""
echo -e "${BLUE}3. Use as credenciais:${NC}"
echo -e "   Email: ${GREEN}admin@gmail.com${NC}"
echo -e "   Senha: ${GREEN}admin123${NC}"
echo ""
echo -e "${YELLOW}Outras credenciais disponíveis:${NC}"
echo -e "   • Email: ${GREEN}gestor@example.com${NC} | Senha: ${GREEN}gestor123${NC}"
echo -e "   • Email: ${GREEN}usuario@example.com${NC} | Senha: ${GREEN}usuario123${NC}"
echo ""

exit 0
