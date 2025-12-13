#!/bin/bash

# Script para ejecutar el pipeline de DevSecOps localmente
# Simula las verificaciones que se ejecutan en GitHub Actions

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Contadores
PASSED=0
FAILED=0
WARNINGS=0

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Pipeline DevSecOps - Ejecución Local${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Función para encontrar PHP
find_php() {
    PHP_PATHS=(
        "/opt/homebrew/Cellar/php/8.5.0/bin/php"
        "/opt/homebrew/bin/php"
        "/usr/bin/php"
        "/usr/local/bin/php"
        "$(which php 2>/dev/null)"
    )
    
    for php_path in "${PHP_PATHS[@]}"; do
        if [ -f "$php_path" ] && [ -x "$php_path" ]; then
            if "$php_path" -v > /dev/null 2>&1; then
                echo "$php_path"
                return 0
            fi
        fi
    done
    return 1
}

PHP_CMD=$(find_php)

if [ -z "$PHP_CMD" ]; then
    echo -e "${RED}❌ PHP no encontrado${NC}"
    exit 1
fi

echo -e "${GREEN}✅ PHP encontrado: $($PHP_CMD -v | head -n 1)${NC}"
echo ""

# Job 1: Verificación de Sintaxis
echo -e "${YELLOW}[1/7] Verificando sintaxis PHP...${NC}"
SYNTAX_ERRORS=0
while IFS= read -r -d '' file; do
    if ! $PHP_CMD -l "$file" > /dev/null 2>&1; then
        echo -e "${RED}  ❌ Error en: $file${NC}"
        $PHP_CMD -l "$file" 2>&1
        SYNTAX_ERRORS=$((SYNTAX_ERRORS + 1))
        FAILED=$((FAILED + 1))
    fi
done < <(find . -name "*.php" -not -path "./vendor/*" -print0)

if [ $SYNTAX_ERRORS -eq 0 ]; then
    echo -e "${GREEN}  ✅ Todas las verificaciones de sintaxis pasaron${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}  ❌ Se encontraron $SYNTAX_ERRORS errores de sintaxis${NC}"
fi
echo ""

# Job 2: Verificación de Estructura
echo -e "${YELLOW}[2/7] Verificando estructura del proyecto...${NC}"
STRUCTURE_OK=true
[ -d "config" ] && echo -e "${GREEN}  ✅ Carpeta config existe${NC}" || { echo -e "${RED}  ❌ Falta carpeta config${NC}"; STRUCTURE_OK=false; }
[ -d "models" ] && echo -e "${GREEN}  ✅ Carpeta models existe${NC}" || { echo -e "${RED}  ❌ Falta carpeta models${NC}"; STRUCTURE_OK=false; }
[ -d "controllers" ] && echo -e "${GREEN}  ✅ Carpeta controllers existe${NC}" || { echo -e "${RED}  ❌ Falta carpeta controllers${NC}"; STRUCTURE_OK=false; }
[ -d "views" ] && echo -e "${GREEN}  ✅ Carpeta views existe${NC}" || { echo -e "${RED}  ❌ Falta carpeta views${NC}"; STRUCTURE_OK=false; }
[ -d "public" ] && echo -e "${GREEN}  ✅ Carpeta public existe${NC}" || { echo -e "${RED}  ❌ Falta carpeta public${NC}"; STRUCTURE_OK=false; }
[ -f "public/index.php" ] && echo -e "${GREEN}  ✅ index.php existe${NC}" || { echo -e "${RED}  ❌ Falta index.php${NC}"; STRUCTURE_OK=false; }

if [ "$STRUCTURE_OK" = true ]; then
    PASSED=$((PASSED + 1))
else
    FAILED=$((FAILED + 1))
fi
echo ""

# Job 3: Verificación de Seguridad - Funciones Peligrosas
echo -e "${YELLOW}[3/7] Buscando funciones peligrosas...${NC}"
DANGEROUS_FUNCTIONS=0
while IFS= read -r file; do
    if grep -q "eval\|exec\|system\|shell_exec\|passthru" "$file" 2>/dev/null; then
        echo -e "${RED}  ⚠️  Función peligrosa encontrada en: $file${NC}"
        DANGEROUS_FUNCTIONS=$((DANGEROUS_FUNCTIONS + 1))
        WARNINGS=$((WARNINGS + 1))
    fi
done < <(find . -name "*.php" -not -path "./vendor/*")

if [ $DANGEROUS_FUNCTIONS -eq 0 ]; then
    echo -e "${GREEN}  ✅ No se encontraron funciones peligrosas${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${YELLOW}  ⚠️  Se encontraron $DANGEROUS_FUNCTIONS archivos con funciones peligrosas${NC}"
fi
echo ""

# Job 4: Verificación de Prepared Statements
echo -e "${YELLOW}[4/7] Verificando uso de prepared statements...${NC}"
UNSAFE_QUERIES=0
while IFS= read -r file; do
    if grep -q "->query(" "$file" 2>/dev/null && ! grep -q "prepare" "$file" 2>/dev/null; then
        echo -e "${YELLOW}  ⚠️  Posible consulta sin prepared statement en: $file${NC}"
        UNSAFE_QUERIES=$((UNSAFE_QUERIES + 1))
        WARNINGS=$((WARNINGS + 1))
    fi
done < <(find models controllers -name "*.php" 2>/dev/null)

if [ $UNSAFE_QUERIES -eq 0 ]; then
    echo -e "${GREEN}  ✅ Todas las consultas usan prepared statements${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${YELLOW}  ⚠️  Se encontraron $UNSAFE_QUERIES posibles consultas sin prepared statements${NC}"
fi
echo ""

# Job 5: Verificación de Sanitización
echo -e "${YELLOW}[5/7] Verificando sanitización de salidas...${NC}"
UNSAFE_OUTPUTS=0
while IFS= read -r file; do
    # Buscar echos sin htmlspecialchars (simplificado)
    if grep -q "echo.*\$" "$file" 2>/dev/null && ! grep -q "htmlspecialchars" "$file" 2>/dev/null; then
        # Verificar si realmente hay salidas sin sanitizar
        if grep -q "echo.*\$_" "$file" 2>/dev/null || grep -q "echo.*\$member\|echo.*\$class\|echo.*\$payment" "$file" 2>/dev/null; then
            echo -e "${YELLOW}  ⚠️  Posible salida sin sanitizar en: $file${NC}"
            UNSAFE_OUTPUTS=$((UNSAFE_OUTPUTS + 1))
            WARNINGS=$((WARNINGS + 1))
        fi
    fi
done < <(find views -name "*.php" 2>/dev/null)

if [ $UNSAFE_OUTPUTS -eq 0 ]; then
    echo -e "${GREEN}  ✅ Las salidas están sanitizadas${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${YELLOW}  ⚠️  Se encontraron $UNSAFE_OUTPUTS posibles salidas sin sanitizar${NC}"
fi
echo ""

# Job 6: Verificación de Base de Datos
echo -e "${YELLOW}[6/7] Verificando script de base de datos...${NC}"
if [ -f "database/schema.sql" ]; then
    echo -e "${GREEN}  ✅ Script SQL encontrado${NC}"
    
    # Verificar que PostgreSQL esté ejecutándose
    if pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
        echo -e "${GREEN}  ✅ PostgreSQL está ejecutándose${NC}"
        
        # Verificar que la base de datos existe
        if psql -d gimnasio_db -c "\dt" > /dev/null 2>&1; then
            TABLES=$(psql -d gimnasio_db -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';" 2>/dev/null | tr -d ' ')
            if [ ! -z "$TABLES" ] && [ "$TABLES" -ge 4 ]; then
                echo -e "${GREEN}  ✅ Base de datos configurada correctamente ($TABLES tablas)${NC}"
                PASSED=$((PASSED + 1))
            else
                echo -e "${YELLOW}  ⚠️  Base de datos tiene menos tablas de las esperadas${NC}"
                WARNINGS=$((WARNINGS + 1))
            fi
        else
            echo -e "${YELLOW}  ⚠️  Base de datos no accesible${NC}"
            WARNINGS=$((WARNINGS + 1))
        fi
    else
        echo -e "${YELLOW}  ⚠️  PostgreSQL no está ejecutándose${NC}"
        WARNINGS=$((WARNINGS + 1))
    fi
else
    echo -e "${RED}  ❌ Script SQL no encontrado${NC}"
    FAILED=$((FAILED + 1))
fi
echo ""

# Job 7: Verificación de Archivos de Configuración
echo -e "${YELLOW}[7/7] Verificando archivos de configuración...${NC}"
CONFIG_OK=true
[ -f "composer.json" ] && echo -e "${GREEN}  ✅ composer.json existe${NC}" || { echo -e "${YELLOW}  ⚠️  composer.json no encontrado${NC}"; CONFIG_OK=false; }
[ -f ".phpcs.xml" ] && echo -e "${GREEN}  ✅ .phpcs.xml existe${NC}" || { echo -e "${YELLOW}  ⚠️  .phpcs.xml no encontrado${NC}"; CONFIG_OK=false; }
[ -f "phpstan.neon" ] && echo -e "${GREEN}  ✅ phpstan.neon existe${NC}" || { echo -e "${YELLOW}  ⚠️  phpstan.neon no encontrado${NC}"; CONFIG_OK=false; }
[ -d ".github/workflows" ] && echo -e "${GREEN}  ✅ Workflows de GitHub Actions configurados${NC}" || { echo -e "${YELLOW}  ⚠️  Workflows no encontrados${NC}"; CONFIG_OK=false; }

if [ "$CONFIG_OK" = true ]; then
    PASSED=$((PASSED + 1))
else
    WARNINGS=$((WARNINGS + 1))
fi
echo ""

# Resumen
echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Resumen del Pipeline${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo -e "${GREEN}✅ Verificaciones pasadas: $PASSED${NC}"
echo -e "${RED}❌ Verificaciones fallidas: $FAILED${NC}"
echo -e "${YELLOW}⚠️  Advertencias: $WARNINGS${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 Pipeline completado exitosamente${NC}"
    echo ""
    echo -e "${BLUE}Próximos pasos:${NC}"
    echo -e "1. Sube el código a GitHub:"
    echo -e "   git add ."
    echo -e "   git commit -m 'Agregar pipeline DevSecOps'"
    echo -e "   git push origin main"
    echo -e ""
    echo -e "2. Ve a la pestaña Actions en GitHub para ver el pipeline ejecutándose"
    exit 0
else
    echo -e "${RED}❌ Pipeline falló. Revisa los errores arriba.${NC}"
    exit 1
fi




