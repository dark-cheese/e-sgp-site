#!/bin/bash

# ============================================================
# Script de Teste Rápido - e-SGP Login
# ============================================================
# Este script executa todos os passos para testar o login

echo "=========================================="
echo "e-SGP - TESTE RÁPIDO DO SISTEMA DE LOGIN"
echo "=========================================="
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuração
MYSQL_HOST="200.131.251.11"
MYSQL_PORT="3341"
MYSQL_USER="2026Iventario"
MYSQL_PASS="Inventa@2026"
MYSQL_DB="2026ProjetoInv"
PYTHON_PORT="5000"
FLASK_URL="http://localhost:${PYTHON_PORT}"

echo -e "${YELLOW}[1/5]${NC} Verificando MySQL..."
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASS $MYSQL_DB -e "SELECT 'MySQL OK' AS status;" 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Conexão MySQL OK${NC}"
else
    echo -e "${RED}✗ Erro ao conectar no MySQL${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}[2/5]${NC} Verificando Python e dependências..."
python3 -c "import flask, flask_sqlalchemy, flask_cors, mysql.connector; print('OK')" 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Dependências Python OK${NC}"
else
    echo -e "${RED}✗ Instale as dependências: pip install -r requirements.txt${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}[3/5]${NC} Iniciando servidor Flask..."
python3 app.py > /tmp/flask.log 2>&1 &
FLASK_PID=$!
sleep 2

# Verificar se o Flask iniciou
if ! kill -0 $FLASK_PID 2>/dev/null; then
    echo -e "${RED}✗ Erro ao iniciar Flask${NC}"
    cat /tmp/flask.log
    exit 1
fi
echo -e "${GREEN}✓ Flask iniciado (PID: $FLASK_PID)${NC}"

echo ""
echo -e "${YELLOW}[4/5]${NC} Executando testes..."
python3 test_login.py

echo ""
echo -e "${YELLOW}[5/5]${NC} Limpeza..."
kill $FLASK_PID 2>/dev/null
echo -e "${GREEN}✓ Servidor Flask desligado${NC}"

echo ""
echo "=========================================="
echo -e "${GREEN}TESTE CONCLUÍDO COM SUCESSO!${NC}"
echo "=========================================="
echo ""
echo "Próximas etapas:"
echo "1. Execute: python3 app.py"
echo "2. Acesse: http://localhost:5000/index.html"
echo "3. Faça login com:"
echo "   Email: admin@gmail.com"
echo "   Senha: admin123"
echo ""
