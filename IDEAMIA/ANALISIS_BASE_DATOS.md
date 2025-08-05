# Análisis Completo de la Base de Datos - Sistema ARAmed

## Estructura de la Base de Datos

### Tablas Principales

#### 1. **configuracion** (Configuración General)
- **Propósito**: Configuración central del sitio web
- **Campos clave**:
  - `title`, `description`: SEO y metadatos
  - `prodspag`: Productos por página
  - `sliderhmin`, `sliderhmax`, `sliderproporcion`, `slideranim`: Configuración del carrusel
  - `telefono`, `telefono1`: Información de contacto
  - `facebook`, `instagram`, `youtube`: Redes sociales
  - `envio`, `envioglobal`: Configuración de envíos
  - `iva`, `incremento`: Configuración fiscal
  - `video`, `videotxt`: Video promocional
  - `about1`, `about2`, `about3`, `about4`: Secciones "Acerca de"
  - `servicios`: Descripción de servicios

#### 2. **productos** (Catálogo de Productos)
- **Propósito**: Gestión del catálogo de simuladores médicos
- **Campos clave**:
  - `categoria`: ID de categoría (FK a productoscat)
  - `titulo`: Nombre del producto
  - `titulo1`: Código del producto
  - `txt`: Descripción detallada
  - `imagen`: Imagen principal
  - `destacado`: Producto destacado (0/1)
  - `estatus`: Estado activo/inactivo
  - `marca`: ID de marca (FK a empresas)
  - `video`: URL de video promocional
  - `pdf`: Archivo PDF técnico
  - `rel`: Productos relacionados

#### 3. **productoscat** (Categorías de Productos)
- **Propósito**: Organización jerárquica de productos
- **Campos clave**:
  - `parent`: Categoría padre (0 = categoría raíz)
  - `txt`: Nombre de la categoría
  - `imagen`, `imagen2`: Imágenes de la categoría
  - `estatus`: Estado activo/inactivo
  - `orden`: Orden de visualización

#### 4. **productospic** (Imágenes de Productos)
- **Propósito**: Galería de imágenes por producto
- **Campos clave**:
  - `producto`: ID del producto (FK a productos)
  - `titulo`, `title`: Títulos de la imagen
  - `txt`: Descripción de la imagen
  - `orden`: Orden de visualización

#### 5. **empresas** (Marcas/Proveedores)
- **Propósito**: Gestión de marcas de simuladores
- **Campos clave**:
  - `nombre`: Nombre de la empresa
  - `url`: Sitio web
  - `titulo`: Nombre para mostrar
  - `estatus`: Estado activo/inactivo
  - `orden`: Orden de visualización

#### 6. **noticias** (Blog/Noticias)
- **Propósito**: Gestión de contenido editorial
- **Campos clave**:
  - `titulo`: Título de la noticia
  - `txt`: Contenido de la noticia
  - `fecha`: Fecha de publicación
  - `estatus`: Estado activo/inactivo
  - `orden`: Orden de visualización

#### 7. **noticiaspic** (Imágenes de Noticias)
- **Propósito**: Imágenes para las noticias
- **Campos clave**:
  - `noticia`: ID de la noticia (FK a noticias)
  - `titulo`: Título de la imagen
  - `orden`: Orden de visualización

#### 8. **carousel** (Slider Principal)
- **Propósito**: Carrusel de imágenes en la página principal
- **Campos clave**:
  - `titulo`: Título de la imagen
  - `txt`: Descripción
  - `url`: Enlace de la imagen
  - `orden`: Orden de visualización

#### 9. **proyectos** (Proyectos Realizados)
- **Propósito**: Portfolio de proyectos
- **Campos clave**:
  - `titulo`: Título del proyecto
  - `txt`: Descripción del proyecto
  - `fecha`: Fecha del proyecto
  - `estatus`: Estado activo/inactivo

#### 10. **servicios** (Servicios Ofrecidos)
- **Propósito**: Gestión de servicios
- **Campos clave**:
  - `titulo`: Nombre del servicio
  - `txt`: Descripción del servicio
  - `imagen1`: Imagen del servicio
  - `estatus`: Estado activo/inactivo
  - `orden`: Orden de visualización

#### 11. **testimonios** (Testimonios de Clientes)
- **Propósito**: Testimonios de clientes
- **Campos clave**:
  - `titulo`: Nombre del cliente
  - `txt`: Testimonio
  - `estatus`: Estado activo/inactivo

#### 12. **user** (Usuarios del Sistema)
- **Propósito**: Autenticación y autorización
- **Campos clave**:
  - `user`: Nombre de usuario
  - `pass`: Contraseña (MD5 - **CRÍTICO**)
  - `nivel`: Nivel de acceso (1=usuario, 2=admin)

#### 13. **aramed** (Configuración Adicional)
- **Propósito**: Configuración adicional del sistema
- **Campos clave**:
  - `txt1`: Textos adicionales
  - `destinatario1`, `destinatario2`: Emails de contacto
  - `remitente`, `pass`, `server`, `port`: Configuración de email
  - `facebook`, `instagram`, `youtube`: Redes sociales

## Análisis de Problemas Identificados

### 1. **Seguridad Crítica**
- ❌ **Contraseñas en MD5**: Extremadamente inseguro
- ❌ **Falta de roles**: Solo 2 niveles (usuario/admin)
- ❌ **No hay auditoría**: Sin logs de acciones
- ❌ **Falta de validación**: No hay restricciones de entrada

### 2. **Estructura de Datos**
- ❌ **Falta de normalización**: Datos duplicados
- ❌ **Sin foreign keys**: No hay integridad referencial
- ❌ **Campos inconsistentes**: Mezcla de tipos de datos
- ❌ **Falta de timestamps**: No hay created_at/updated_at

### 3. **Escalabilidad**
- ❌ **Sin índices optimizados**: Performance pobre
- ❌ **Falta de paginación**: No hay límites de consulta
- ❌ **Sin cache**: Consultas repetitivas
- ❌ **No hay soft deletes**: Eliminación permanente

### 4. **Funcionalidad**
- ❌ **Sin sistema de pedidos**: No hay e-commerce
- ❌ **Sin gestión de clientes**: No hay CRM
- ❌ **Sin facturación**: No hay sistema fiscal
- ❌ **Sin inventario**: No hay control de stock

## Propuesta de Mejoras en la Base de Datos

### 1. **Nuevas Tablas Necesarias**

#### **pedidos** (Sistema de Pedidos)
```sql
CREATE TABLE pedidos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'),
  fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_envio TIMESTAMP NULL,
  direccion_envio TEXT,
  notas TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);
```

#### **clientes** (Gestión de Clientes)
```sql
CREATE TABLE clientes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  telefono VARCHAR(20),
  rfc VARCHAR(13),
  direccion TEXT,
  empresa VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### **pedidos_productos** (Detalles de Pedidos)
```sql
CREATE TABLE pedidos_productos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);
```

#### **facturas** (Sistema Fiscal)
```sql
CREATE TABLE facturas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT NOT NULL,
  uuid VARCHAR(255) UNIQUE,
  cfdi TEXT,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('vigente', 'cancelado'),
  fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);
```

#### **cartas_porte** (Carta Porte Electrónica)
```sql
CREATE TABLE cartas_porte (
  id INT PRIMARY KEY AUTO_INCREMENT,
  pedido_id INT NOT NULL,
  uuid VARCHAR(255) UNIQUE,
  cfdi TEXT,
  estado ENUM('vigente', 'cancelado'),
  fecha_carta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);
```

#### **audit_log** (Logs de Auditoría)
```sql
CREATE TABLE audit_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(100) NOT NULL,
  description TEXT,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(id)
);
```

#### **roles** (Sistema de Roles)
```sql
CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  descripcion TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **user_roles** (Relación Usuario-Rol)
```sql
CREATE TABLE user_roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  role_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(id),
  FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

### 2. **Mejoras en Tablas Existentes**

#### **user** (Mejorar Seguridad)
```sql
ALTER TABLE user 
ADD COLUMN email VARCHAR(255) UNIQUE,
ADD COLUMN password_hash VARCHAR(255) NOT NULL,
ADD COLUMN password_salt VARCHAR(255),
ADD COLUMN last_login TIMESTAMP NULL,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
DROP COLUMN pass;
```

#### **productos** (Mejorar Estructura)
```sql
ALTER TABLE productos 
ADD COLUMN precio DECIMAL(10,2),
ADD COLUMN stock INT DEFAULT 0,
ADD COLUMN sku VARCHAR(100) UNIQUE,
ADD COLUMN peso DECIMAL(8,2),
ADD COLUMN dimensiones VARCHAR(100),
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD INDEX idx_categoria (categoria),
ADD INDEX idx_marca (marca),
ADD INDEX idx_destacado (destacado),
ADD INDEX idx_estatus (estatus);
```

### 3. **Índices Optimizados**
```sql
-- Índices para mejorar performance
CREATE INDEX idx_productos_categoria ON productos(categoria);
CREATE INDEX idx_productos_marca ON productos(marca);
CREATE INDEX idx_productos_destacado ON productos(destacado);
CREATE INDEX idx_productos_estatus ON productos(estatus);
CREATE INDEX idx_productoscat_parent ON productoscat(parent);
CREATE INDEX idx_productospic_producto ON productospic(producto);
CREATE INDEX idx_noticias_fecha ON noticias(fecha);
CREATE INDEX idx_noticias_estatus ON noticias(estatus);
```

### 4. **Triggers para Auditoría**
```sql
-- Trigger para audit_log en productos
DELIMITER //
CREATE TRIGGER productos_audit_insert 
AFTER INSERT ON productos
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (user_id, action, description) 
  VALUES (1, 'INSERT', CONCAT('Producto creado: ', NEW.titulo));
END//

CREATE TRIGGER productos_audit_update 
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (user_id, action, description) 
  VALUES (1, 'UPDATE', CONCAT('Producto actualizado: ', NEW.titulo));
END//

CREATE TRIGGER productos_audit_delete 
AFTER DELETE ON productos
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (user_id, action, description) 
  VALUES (1, 'DELETE', CONCAT('Producto eliminado: ', OLD.titulo));
END//
DELIMITER ;
```

## Migración de Datos

### 1. **Migración de Usuarios**
```sql
-- Migrar contraseñas MD5 a bcrypt
UPDATE user SET 
password_hash = '$2y$10$' || MD5(CONCAT(pass, RAND())),
password_salt = MD5(RAND()),
email = CONCAT(user, '@aramed.com')
WHERE pass IS NOT NULL;
```

### 2. **Migración de Productos**
```sql
-- Agregar campos faltantes
UPDATE productos SET 
precio = 0.00,
stock = 1,
sku = CONCAT('PROD-', LPAD(id, 6, '0'))
WHERE precio IS NULL;
```

## Plan de Implementación

### Fase 1: Seguridad (Semana 1)
- [ ] Crear nuevas tablas de seguridad
- [ ] Migrar usuarios a sistema seguro
- [ ] Implementar roles y permisos
- [ ] Configurar auditoría

### Fase 2: E-commerce (Semana 2-3)
- [ ] Crear tablas de pedidos y clientes
- [ ] Implementar carrito de compras
- [ ] Sistema de pagos
- [ ] Gestión de envíos

### Fase 3: Fiscal (Semana 4)
- [ ] Integrar tablas de facturación
- [ ] Conectar con Enlace Fiscal
- [ ] Sistema de carta porte
- [ ] Reportes fiscales

### Fase 4: Optimización (Semana 5)
- [ ] Agregar índices
- [ ] Implementar cache
- [ ] Optimizar consultas
- [ ] Backup automático

## Métricas de Éxito

### Seguridad
- [ ] 0 contraseñas en MD5
- [ ] 100% de acciones auditadas
- [ ] Sistema de roles implementado
- [ ] Logs de seguridad activos

### Performance
- [ ] Tiempo de consulta < 100ms
- [ ] Índices optimizados
- [ ] Cache implementado
- [ ] Backup automático

### Funcionalidad
- [ ] Sistema de pedidos funcionando
- [ ] Facturación electrónica activa
- [ ] CRM implementado
- [ ] API REST completa 