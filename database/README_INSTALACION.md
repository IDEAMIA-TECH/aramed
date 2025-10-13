# 📋 INSTALACIÓN DE BASE DE DATOS - ARAMED Y LABORATORIOS

**Fecha:** 13 de Octubre 2025  
**Base de datos:** aramed2025_produccion

---

## 📦 ARCHIVOS DISPONIBLES

### 1. `setup_database.sql` (RECOMENDADO)
- **Descripción:** Script completo con comentarios detallados
- **Tamaño:** ~10 KB
- **Contenido:** 
  - 3 tablas con estructura completa
  - Comentarios explicativos
  - Datos de ejemplo (comentados)
  - Verificación de instalación
- **Recomendado para:** Primera instalación o desarrollo

### 2. `setup_database_simple.sql`
- **Descripción:** Script limpio sin comentarios extensos
- **Tamaño:** ~3 KB
- **Contenido:** 
  - 3 tablas con estructura básica
  - Sin comentarios
  - Sin datos de ejemplo
- **Recomendado para:** Producción o instalaciones rápidas

---

## 🎯 TABLAS QUE SE CREARÁN

| # | Tabla | Propósito | Campos principales |
|---|-------|-----------|-------------------|
| 1 | `newsletter_subscriptions` | Suscripciones al newsletter | institucion, email_oficial, producto_interes |
| 2 | `contact_messages` | Mensajes de contacto general | nombre, email, asunto, mensaje |
| 3 | `contact_quotes` | Solicitudes de cotización | nombre, email, productos, presupuesto |

---

## 🚀 MÉTODOS DE INSTALACIÓN

### Método 1: phpMyAdmin (Recomendado)

#### Paso 1: Acceder a phpMyAdmin
1. Ir a: https://aramedylaboratorio.com:2083/
2. Iniciar sesión con credenciales de cPanel
3. Click en "phpMyAdmin"

#### Paso 2: Seleccionar Base de Datos
1. En el panel izquierdo, click en: `aramed2025_produccion`
2. Verificar que estás en la base de datos correcta (aparece arriba)

#### Paso 3: Ejecutar Script
1. Click en la pestaña "SQL" (arriba)
2. Abrir el archivo `setup_database.sql` en un editor de texto
3. Copiar TODO el contenido del archivo
4. Pegarlo en el área de texto de phpMyAdmin
5. Click en el botón "Continuar" o "Go"

#### Paso 4: Verificar Instalación
1. En el panel izquierdo, actualizar (F5)
2. Deberías ver las 3 tablas nuevas:
   - ✅ newsletter_subscriptions
   - ✅ contact_messages
   - ✅ contact_quotes

---

### Método 2: Línea de Comandos (Avanzado)

#### Requisitos
- Acceso SSH al servidor
- Credenciales de MySQL

#### Comandos

```bash
# 1. Subir el archivo SQL al servidor (desde tu computadora local)
scp setup_database.sql usuario@aramedylaboratorio.com:/tmp/

# 2. Conectar al servidor via SSH
ssh usuario@aramedylaboratorio.com

# 3. Ejecutar el script SQL
mysql -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion < /tmp/setup_database.sql

# Cuando se solicite, ingresar password: pmDLi&PB$zntrzJ4

# 4. Verificar que se crearon las tablas
mysql -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion -e "SHOW TABLES;"

# 5. Limpiar archivo temporal
rm /tmp/setup_database.sql
```

---

### Método 3: PHP Script (Alternativo)

Si prefieres ejecutar vía web, puedes usar el archivo `install-database.php` que ya está creado.

#### Pasos:
1. Subir `install-database.php` a: `/public_html/`
2. Acceder a: https://aramedylaboratorio.com/NUEVO/aramed/public_html/install-database.php
3. Click en "Install Database Tables"
4. **IMPORTANTE:** Eliminar el archivo después de usarlo por seguridad

---

## ✅ VERIFICACIÓN DE INSTALACIÓN

### Checklist

- [ ] **newsletter_subscriptions** existe
  - [ ] Tiene 20+ campos
  - [ ] email_oficial es UNIQUE
  - [ ] Índices creados correctamente

- [ ] **contact_messages** existe
  - [ ] Tiene 13+ campos
  - [ ] FULLTEXT index en 'mensaje'
  - [ ] Status field con enum

- [ ] **contact_quotes** existe
  - [ ] Tiene 16+ campos
  - [ ] Campo 'productos' para JSON
  - [ ] Status field con enum

### Comandos de Verificación

```sql
-- Ver todas las tablas
SHOW TABLES;

-- Ver estructura de newsletter_subscriptions
DESCRIBE newsletter_subscriptions;

-- Ver estructura de contact_messages
DESCRIBE contact_messages;

-- Ver estructura de contact_quotes
DESCRIBE contact_quotes;

-- Contar registros (debe ser 0 si es instalación nueva)
SELECT COUNT(*) as newsletter FROM newsletter_subscriptions;
SELECT COUNT(*) as contact FROM contact_messages;
SELECT COUNT(*) as quotes FROM contact_quotes;

-- Ver índices
SHOW INDEX FROM newsletter_subscriptions;
SHOW INDEX FROM contact_messages;
SHOW INDEX FROM contact_quotes;
```

---

## 🔧 TROUBLESHOOTING

### Error: "Table already exists"
**Causa:** Las tablas ya existen en la base de datos  
**Solución:**
1. Si quieres mantener datos existentes: No hacer nada
2. Si quieres recrear las tablas:
   ```sql
   DROP TABLE IF EXISTS newsletter_subscriptions;
   DROP TABLE IF EXISTS contact_messages;
   DROP TABLE IF EXISTS contact_quotes;
   -- Luego ejecutar setup_database.sql de nuevo
   ```

### Error: "Access denied"
**Causa:** Credenciales incorrectas  
**Solución:**
1. Verificar credenciales en `includes/config.php`
2. Verificar que el usuario tiene permisos de CREATE TABLE

### Error: "Syntax error"
**Causa:** Versión de MySQL incompatible o copia incompleta  
**Solución:**
1. Asegurarse de copiar TODO el contenido del archivo SQL
2. Verificar versión de MySQL (mínimo 5.6)
3. Usar `setup_database_simple.sql` como alternativa

### Error: "Character set"
**Causa:** MySQL no soporta utf8mb4  
**Solución:**
```sql
SET NAMES utf8;
-- Y cambiar en el script: utf8mb4 -> utf8
```

---

## 📊 PRUEBAS POST-INSTALACIÓN

### 1. Insertar Datos de Prueba

```sql
-- Test newsletter
INSERT INTO newsletter_subscriptions 
(institucion, tipo_institucion, estado, ciudad, nombre, puesto, email_oficial, telefono_oficina) 
VALUES 
('Test Hospital', 'Hospital', 'Test', 'Test', 'Test User', 'Test', 'test@test.com', '1234567890');

-- Verificar
SELECT * FROM newsletter_subscriptions WHERE email_oficial = 'test@test.com';

-- Limpiar
DELETE FROM newsletter_subscriptions WHERE email_oficial = 'test@test.com';
```

### 2. Probar desde Formularios Web

1. Ir a: https://aramedylaboratorio.com/NUEVO/aramed/public_html/
2. Scroll hasta el formulario de newsletter
3. Llenar todos los campos
4. Click en "Enviar"
5. Verificar en phpMyAdmin que se insertó el registro

---

## 📁 RESPALDO Y MANTENIMIENTO

### Crear Respaldo

```sql
-- Exportar todas las tablas
mysqldump -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion \
  newsletter_subscriptions contact_messages contact_quotes > backup_$(date +%Y%m%d).sql
```

### Restaurar Respaldo

```bash
mysql -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion < backup_20251013.sql
```

### Mantenimiento Programado

```sql
-- Optimizar tablas (ejecutar mensualmente)
OPTIMIZE TABLE newsletter_subscriptions;
OPTIMIZE TABLE contact_messages;
OPTIMIZE TABLE contact_quotes;

-- Analizar índices (ejecutar mensualmente)
ANALYZE TABLE newsletter_subscriptions;
ANALYZE TABLE contact_messages;
ANALYZE TABLE contact_quotes;

-- Limpiar registros antiguos (ejemplo: >2 años)
DELETE FROM contact_messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR) AND status = 'cerrado';
```

---

## 🔐 SEGURIDAD

### Recomendaciones

1. **Respaldos automáticos**
   - Configurar backup diario en cPanel
   - Mantener al menos 7 días de historial

2. **Limitar acceso**
   - Solo usuarios autorizados deben tener acceso a phpMyAdmin
   - Usar contraseñas fuertes

3. **Monitoreo**
   - Revisar logs de errores regularmente
   - Monitorear crecimiento de tablas

4. **Prepared Statements**
   - Los handlers PHP ya usan prepared statements
   - NUNCA concatenar valores directamente en queries

---

## 📞 SOPORTE

### Archivos de Referencia
- `includes/newsletter_handler.php` - Backend newsletter
- `includes/contact_handler.php` - Backend contacto
- `includes/config.php` - Configuración de BD
- `includes/connection.php` - Conexión PDO

### Credenciales de Producción
```php
DB_HOST:     173.231.22.109
DB_NAME:     aramed2025_produccion
DB_USER:     aramed2025_prod
DB_PASS:     pmDLi&PB$zntrzJ4
```

---

## ✅ CHECKLIST FINAL

- [ ] Script SQL ejecutado correctamente
- [ ] 3 tablas creadas y verificadas
- [ ] Índices creados correctamente
- [ ] Prueba de inserción exitosa
- [ ] Formularios web funcionando
- [ ] Emails de notificación recibidos
- [ ] Respaldo inicial creado
- [ ] Archivo install-database.php eliminado (si se usó)

---

**¡Listo! La base de datos está configurada y lista para usar.**

**Última actualización:** 13 de Octubre 2025 - 20:30 hrs

