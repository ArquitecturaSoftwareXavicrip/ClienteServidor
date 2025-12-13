#!/bin/bash

# Script para configurar PostgreSQL y la base de datos del proyecto

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Configuración de Base de Datos${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Verificar si PostgreSQL está instalado
echo -e "${YELLOW}[1/5] Verificando PostgreSQL...${NC}"
if command -v psql &> /dev/null; then
    PSQL_VERSION=$(psql --version)
    echo -e "${GREEN}✅ PostgreSQL encontrado: $PSQL_VERSION${NC}"
else
    echo -e "${RED}❌ PostgreSQL no está instalado${NC}"
    echo ""
    echo -e "${YELLOW}Instalando PostgreSQL con Homebrew...${NC}"
    if brew install postgresql@14; then
        echo -e "${GREEN}✅ PostgreSQL instalado${NC}"
        echo -e "${YELLOW}Agregando PostgreSQL al PATH...${NC}"
        echo 'export PATH="/opt/homebrew/opt/postgresql@14/bin:$PATH"' >> ~/.zshrc
        export PATH="/opt/homebrew/opt/postgresql@14/bin:$PATH"
    else
        echo -e "${RED}❌ Error al instalar PostgreSQL${NC}"
        exit 1
    fi
fi

# Verificar si PostgreSQL está ejecutándose
echo -e "${YELLOW}[2/5] Verificando si PostgreSQL está ejecutándose...${NC}"
if pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
    echo -e "${GREEN}✅ PostgreSQL está ejecutándose${NC}"
else
    echo -e "${YELLOW}⚠️  PostgreSQL no está ejecutándose${NC}"
    echo -e "${YELLOW}Iniciando PostgreSQL...${NC}"
    
    # Intentar iniciar con brew services
    if brew services start postgresql@14 2>/dev/null; then
        echo -e "${GREEN}✅ PostgreSQL iniciado${NC}"
        sleep 2
    else
        # Intentar iniciar manualmente
        echo -e "${YELLOW}Intentando iniciar PostgreSQL manualmente...${NC}"
        pg_ctl -D /opt/homebrew/var/postgresql@14 start 2>/dev/null || \
        pg_ctl -D /usr/local/var/postgresql@14 start 2>/dev/null || \
        echo -e "${RED}❌ No se pudo iniciar PostgreSQL automáticamente${NC}"
        echo -e "${YELLOW}Por favor, inicia PostgreSQL manualmente:${NC}"
        echo -e "  brew services start postgresql@14"
        echo -e "  o"
        echo -e "  pg_ctl -D /opt/homebrew/var/postgresql@14 start"
    fi
fi

# Esperar a que PostgreSQL esté listo
echo -e "${YELLOW}Esperando a que PostgreSQL esté listo...${NC}"
for i in {1..10}; do
    if pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
        echo -e "${GREEN}✅ PostgreSQL está listo${NC}"
        break
    fi
    sleep 1
done

# Crear la base de datos si no existe
echo -e "${YELLOW}[3/5] Verificando base de datos...${NC}"
if psql -U postgres -lqt | cut -d \| -f 1 | grep -qw gimnasio_db; then
    echo -e "${GREEN}✅ Base de datos 'gimnasio_db' ya existe${NC}"
else
    echo -e "${YELLOW}Creando base de datos 'gimnasio_db'...${NC}"
    if createdb -U postgres gimnasio_db 2>/dev/null || \
       psql -U postgres -c "CREATE DATABASE gimnasio_db;" 2>/dev/null; then
        echo -e "${GREEN}✅ Base de datos creada${NC}"
    else
        echo -e "${RED}❌ Error al crear la base de datos${NC}"
        echo -e "${YELLOW}Intentando crear como usuario actual...${NC}"
        createdb gimnasio_db 2>/dev/null && echo -e "${GREEN}✅ Base de datos creada${NC}" || \
        echo -e "${RED}❌ No se pudo crear la base de datos${NC}"
        echo -e "${YELLOW}Por favor, créala manualmente:${NC}"
        echo -e "  createdb gimnasio_db"
        echo -e "  o"
        echo -e "  psql -U postgres -c 'CREATE DATABASE gimnasio_db;'"
    fi
fi

# Ejecutar el script SQL
echo -e "${YELLOW}[4/5] Ejecutando script SQL...${NC}"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SQL_FILE="$SCRIPT_DIR/database/schema.sql"

if [ -f "$SQL_FILE" ]; then
    if psql -U postgres -d gimnasio_db -f "$SQL_FILE" > /dev/null 2>&1 || \
       psql -d gimnasio_db -f "$SQL_FILE" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Script SQL ejecutado correctamente${NC}"
    else
        echo -e "${YELLOW}⚠️  Error al ejecutar script SQL como usuario postgres${NC}"
        echo -e "${YELLOW}Intentando como usuario actual...${NC}"
        if psql -d gimnasio_db -f "$SQL_FILE" 2>&1; then
            echo -e "${GREEN}✅ Script SQL ejecutado correctamente${NC}"
        else
            echo -e "${RED}❌ Error al ejecutar script SQL${NC}"
            echo -e "${YELLOW}Ejecuta manualmente:${NC}"
            echo -e "  psql -U postgres -d gimnasio_db -f database/schema.sql"
        fi
    fi
else
    echo -e "${RED}❌ Archivo SQL no encontrado: $SQL_FILE${NC}"
fi

# Verificar tablas creadas
echo -e "${YELLOW}[5/5] Verificando tablas...${NC}"
TABLES=$(psql -U postgres -d gimnasio_db -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';" 2>/dev/null | tr -d ' ' || \
         psql -d gimnasio_db -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';" 2>/dev/null | tr -d ' ')

if [ ! -z "$TABLES" ] && [ "$TABLES" -gt 0 ]; then
    echo -e "${GREEN}✅ Se encontraron $TABLES tabla(s) en la base de datos${NC}"
    echo ""
    echo -e "${BLUE}Tablas creadas:${NC}"
    psql -U postgres -d gimnasio_db -c "\dt" 2>/dev/null || \
    psql -d gimnasio_db -c "\dt" 2>/dev/null || \
    echo "No se pudieron listar las tablas"
else
    echo -e "${YELLOW}⚠️  No se encontraron tablas${NC}"
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Configuración completada${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}Próximos pasos:${NC}"
echo -e "1. Verifica la configuración en config/database.php"
echo -e "2. Ejecuta: ./run.sh"
echo -e "3. Abre: http://localhost:8000/index.php"
echo ""




