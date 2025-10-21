# 📋 INSTRUCCIONES PARA MIGRAR BASE DE DATOS DEL CATÁLOGO

## 🎯 Objetivo
Crear las tablas del nuevo catálogo de productos en la base de datos del servidor.

## 📁 Archivos Disponibles

### **Scripts PHP (Recomendados):**
1. **`crear_tablas_catalogo.php`** - Solo crea las tablas (recomendado para empezar)
2. **`ejecutar_migracion_bd.php`** - Crea tablas + migra datos del sistema viejo
3. **`verificar_migracion_catalogo.php`** - Verifica que todo esté correcto

### **Scripts SQL:**
1. **`nueva_estructura_catalogo.sql`** - Estructura de tablas
2. **`migracion_datos_catalogo.sql`** - Migración de datos

## 🚀 Opción 1: Solo Crear Tablas (Recomendado)

### **Paso 1:** Subir archivos al servidor
```bash
# Subir estos archivos al servidor web:
database/crear_tablas_catalogo.php
database/nueva_estructura_catalogo.sql
```

### **Paso 2:** Ejecutar desde el navegador
```
https://tu-sitio.com/database/crear_tablas_catalogo.php
```

### **Paso 3:** Verificar resultado
El script mostrará:
- ✅ Conexión exitosa a la base de datos
- ✅ Tablas creadas correctamente
- ✅ Conteo de registros en cada tabla

## 🚀 Opción 2: Crear Tablas + Migrar Datos

### **Paso 1:** Subir archivos al servidor
```bash
# Subir estos archivos al servidor web:
database/ejecutar_migracion_bd.php
database/nueva_estructura_catalogo.sql
database/migracion_datos_catalogo.sql
```

### **Paso 2:** Ejecutar desde el navegador
```
https://tu-sitio.com/database/ejecutar_migracion_bd.php
```

### **Paso 3:** Verificar resultado
El script mostrará:
- ✅ Estructura creada
- ✅ Datos migrados del sistema viejo
- ✅ Estadísticas de migración

## 🔧 Configuración de Base de Datos

### **Actualizar Credenciales:**
Editar los archivos PHP y cambiar estas líneas:

```php
$db_config = [
    'host' => 'localhost',                    // Tu servidor de BD
    'dbname' => 'aramed2025_aramed_db',      // Tu base de datos
    'username' => 'aramed2025_aramed_user',  // Tu usuario
    'password' => 'Aramed2025!'              // Tu contraseña
];
```

## 📊 Tablas que se Crearán

### **Tablas Principales:**
1. **`catalogo_marcas`** - Marcas de productos
2. **`catalogo_categorias`** - Categorías de uso
3. **`catalogo_productos`** - Productos principales
4. **`catalogo_producto_imagenes`** - Relaciones de imágenes
5. **`catalogo_producto_documentos`** - Documentos PDF
6. **`catalogo_filtros`** - Sistema de filtros
7. **`catalogo_producto_stats`** - Estadísticas

### **Datos Iniciales:**
- **19 marcas** principales
- **10 categorías** de uso
- Estructura preparada para migrar **882 productos**

## ✅ Verificación Post-Migración

### **Opción 1: Script Automático**
```
https://tu-sitio.com/database/verificar_migracion_catalogo.php
```

### **Opción 2: Verificación Manual**
```sql
-- Verificar tablas creadas
SHOW TABLES LIKE 'catalogo_%';

-- Verificar registros
SELECT COUNT(*) FROM catalogo_marcas;
SELECT COUNT(*) FROM catalogo_categorias;
SELECT COUNT(*) FROM catalogo_productos;
```

## 🚨 Solución de Problemas

### **Error de Conexión:**
- Verificar credenciales de base de datos
- Confirmar que la base de datos existe
- Verificar permisos del usuario

### **Error de Permisos:**
- El usuario debe tener permisos CREATE TABLE
- El usuario debe tener permisos INSERT
- El usuario debe tener permisos SELECT

### **Error de Tablas Existentes:**
- Las tablas ya existen → Verificar si están vacías
- Si tienen datos → Hacer backup antes de continuar

## 📋 Checklist de Migración

### **Antes de Ejecutar:**
- [ ] Backup de la base de datos actual
- [ ] Credenciales de BD actualizadas en los scripts
- [ ] Archivos subidos al servidor
- [ ] Permisos de usuario verificados

### **Durante la Ejecución:**
- [ ] Script ejecutándose sin errores
- [ ] Mensajes de éxito para cada tabla
- [ ] Conteo de registros correcto

### **Después de Ejecutar:**
- [ ] Todas las tablas creadas
- [ ] Datos migrados (si aplica)
- [ ] Verificación exitosa
- [ ] Archivos temporales eliminados

## 🎯 Resultado Esperado

### **Si todo sale bien:**
```
✅ ÉXITO: Todas las tablas fueron creadas correctamente

📋 Próximos pasos:
   1. Ejecutar migración de datos (opcional)
   2. Crear páginas de catálogo
   3. Implementar funcionalidades
```

### **Estructura Final:**
```
Base de Datos: aramed2025_aramed_db
├── catalogo_marcas (19 registros)
├── catalogo_categorias (10 registros)
├── catalogo_productos (0 registros - listo para migrar)
├── catalogo_producto_imagenes (0 registros)
├── catalogo_producto_documentos (0 registros)
├── catalogo_filtros (0 registros)
└── catalogo_producto_stats (0 registros)
```

## 📞 Soporte

### **Si tienes problemas:**
1. Verificar logs de error del servidor
2. Revisar permisos de base de datos
3. Confirmar que los archivos están en el lugar correcto
4. Verificar que la base de datos existe

### **Archivos de Log:**
- Los scripts muestran progreso en tiempo real
- Cada error se reporta individualmente
- Estadísticas finales se generan automáticamente

---

## 🚀 ¡Listo para Ejecutar!

**Recomendación:** Empezar con `crear_tablas_catalogo.php` para verificar que todo funciona correctamente, y luego ejecutar `ejecutar_migracion_bd.php` si quieres migrar los datos del sistema viejo.
