# 📋 INSTRUCCIONES PARA CREAR TABLAS DEL CATÁLOGO

## 🎯 Objetivo
Crear las tablas del nuevo catálogo de productos en la base de datos del servidor.

## 🚀 Scripts Disponibles

### **1. Crear Tablas (Paso 1)**
**Archivo:** `crear_tablas_catalogo.php`
**URL:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html/crear_tablas_catalogo.php`

**¿Qué hace?**
- Crea las 7 tablas del catálogo
- Verifica que se crearon correctamente
- Muestra estadísticas de cada tabla

### **2. Migrar Datos (Paso 2)**
**Archivo:** `migrar_datos_catalogo.php`
**URL:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html/migrar_datos_catalogo.php`

**¿Qué hace?**
- Migra datos del sistema viejo al nuevo
- Convierte 882 productos al nuevo formato
- Migra 19 marcas y 10 categorías

## 📋 Pasos a Seguir

### **Paso 1: Crear Tablas**
1. Abrir en el navegador:
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/crear_tablas_catalogo.php
   ```

2. Verificar que aparezca:
   ```
   ✅ ÉXITO: Todas las tablas fueron creadas correctamente
   ```

3. Verificar que se muestren las 7 tablas:
   - `catalogo_marcas`
   - `catalogo_categorias`
   - `catalogo_productos`
   - `catalogo_producto_imagenes`
   - `catalogo_producto_documentos`
   - `catalogo_filtros`
   - `catalogo_producto_stats`

### **Paso 2: Migrar Datos (Opcional)**
1. Abrir en el navegador:
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/migrar_datos_catalogo.php
   ```

2. Verificar que aparezca:
   ```
   ✅ MIGRACIÓN EXITOSA
   ```

3. Verificar estadísticas:
   - Sistema viejo: 882 productos, 19 marcas, 10 categorías
   - Sistema nuevo: Datos migrados correctamente

## 🔧 Configuración

### **Credenciales de Base de Datos:**
Los scripts ya están configurados con las credenciales correctas:
- **Host:** 173.231.22.109
- **Base de datos:** aramed2025_produccion
- **Usuario:** aramed2025_prod
- **Contraseña:** pmDLi&PB$zntrzJ4

### **Archivos Requeridos:**
Los scripts necesitan estos archivos en el directorio `database/`:
- `nueva_estructura_catalogo.sql`
- `migracion_datos_catalogo.sql`

## 📊 Tablas que se Crearán

### **1. catalogo_marcas**
- Almacena las marcas de productos
- 19 marcas principales
- Campos: id, nombre, slug, descripcion, logo, estado

### **2. catalogo_categorias**
- Almacena las categorías de uso
- 10 categorías principales
- Campos: id, nombre, slug, descripcion, icono, estado

### **3. catalogo_productos**
- Almacena los productos principales
- 882 productos del sistema viejo
- Campos: id, nombre, slug, descripcion, precio, marca_id, categoria_id, etc.

### **4. catalogo_producto_imagenes**
- Relaciona productos con imágenes
- Campos: id, producto_id, imagen_url, tipo, orden, es_principal

### **5. catalogo_producto_documentos**
- Relaciona productos con documentos PDF
- Campos: id, producto_id, documento_url, tipo, nombre, tamaño

### **6. catalogo_filtros**
- Sistema de filtros dinámicos
- Campos: id, nombre, tipo, opciones, estado

### **7. catalogo_producto_stats**
- Estadísticas de productos
- Campos: id, producto_id, vistas, descargas, favoritos

## ✅ Verificación

### **Después del Paso 1:**
Verificar que aparezcan estas tablas en la base de datos:
```sql
SHOW TABLES LIKE 'catalogo_%';
```

### **Después del Paso 2:**
Verificar que los datos se migraron:
```sql
SELECT COUNT(*) FROM catalogo_productos;
SELECT COUNT(*) FROM catalogo_marcas;
SELECT COUNT(*) FROM catalogo_categorias;
```

## 🚨 Solución de Problemas

### **Error de Conexión:**
- Verificar que el servidor esté funcionando
- Confirmar que la base de datos existe
- Verificar permisos del usuario

### **Error de Permisos:**
- El usuario debe tener permisos CREATE TABLE
- El usuario debe tener permisos INSERT
- El usuario debe tener permisos SELECT

### **Error de Tablas Existentes:**
- Si las tablas ya existen, verificar si están vacías
- Si tienen datos, hacer backup antes de continuar

## 📋 Checklist

### **Antes de Ejecutar:**
- [ ] Backup de la base de datos actual
- [ ] Archivos SQL en el directorio database/
- [ ] Scripts PHP en el directorio public_html/
- [ ] Permisos de usuario verificados

### **Después del Paso 1:**
- [ ] Todas las 7 tablas creadas
- [ ] Mensaje de éxito mostrado
- [ ] Conteo de registros correcto (0 en tablas principales)

### **Después del Paso 2:**
- [ ] Datos migrados correctamente
- [ ] Estadísticas mostradas
- [ ] Mensaje de migración exitosa

## 🎯 Resultado Esperado

### **Si todo sale bien:**
```
✅ ÉXITO: Todas las tablas fueron creadas correctamente
✅ MIGRACIÓN EXITOSA: Datos migrados correctamente
✅ Sistema listo para uso
```

### **Estructura Final:**
```
Base de Datos: aramed2025_produccion
├── catalogo_marcas (19 registros)
├── catalogo_categorias (10 registros)
├── catalogo_productos (882 registros)
├── catalogo_producto_imagenes (relaciones)
├── catalogo_producto_documentos (relaciones)
├── catalogo_filtros (sistema de filtros)
└── catalogo_producto_stats (estadísticas)
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

**Recomendación:** Empezar con el Paso 1 para crear las tablas, y luego ejecutar el Paso 2 si quieres migrar los datos del sistema viejo.
