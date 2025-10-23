# Panel de Administración - Aramed

## 🔐 Sistema de Autenticación

El panel de administración está protegido con un sistema de autenticación robusto que incluye:

### Características de Seguridad

- **Autenticación obligatoria**: Todas las páginas requieren login
- **Sesiones seguras**: Timeout automático después de 8 horas
- **Contraseñas encriptadas**: Uso de `password_hash()` de PHP
- **Protección CSRF**: Tokens de seguridad en formularios
- **Headers de seguridad**: Configuración de seguridad HTTP

### Credenciales por Defecto

Al instalar el sistema, se crea automáticamente un usuario administrador:

- **Usuario**: `admin`
- **Email**: `admin@aramedylaboratorio.com`
- **Contraseña**: `admin123`
- **Rol**: `admin`

⚠️ **IMPORTANTE**: Cambiar la contraseña por defecto inmediatamente después de la instalación.

### Estructura de Archivos

```
admin/
├── login.php              # Página de login
├── logout.php             # Cerrar sesión
├── auth_check.php         # Verificación de autenticación
├── index.php              # Dashboard principal
├── .htaccess              # Protección del directorio
├── README.md              # Esta documentación
└── blog/                  # Gestión del blog
    ├── index.php          # Lista de artículos
    ├── create.php         # Crear artículo
    ├── edit.php           # Editar artículo
    ├── categorias.php     # Gestión de categorías
    ├── comentarios.php    # Gestión de comentarios
    ├── image-manager.php  # Gestor de imágenes
    └── upload-image.php   # Subir imágenes
```

### Base de Datos

El sistema crea automáticamente la tabla `admin_usuarios` con la siguiente estructura:

```sql
CREATE TABLE admin_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'editor') DEFAULT 'editor',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Roles de Usuario

- **Admin**: Acceso completo a todas las funcionalidades
- **Editor**: Acceso limitado a gestión de contenido

### Funcionalidades del Dashboard

- **Estadísticas en tiempo real**: Artículos, comentarios, vistas
- **Acciones rápidas**: Crear contenido, moderar comentarios
- **Artículos recientes**: Lista de últimos artículos creados
- **Comentarios recientes**: Moderación de comentarios pendientes

### Configuración de Seguridad

1. **Cambiar contraseña por defecto**
2. **Configurar IPs permitidas** en `.htaccess`
3. **Usar HTTPS** en producción
4. **Configurar backup** de la base de datos
5. **Monitorear logs** de acceso

### Solución de Problemas

#### Error de Conexión a Base de Datos
- Verificar configuración en `includes/config.php`
- Comprobar credenciales de la base de datos

#### Sesión Expirada
- El sistema redirige automáticamente al login
- Las sesiones expiran después de 8 horas de inactividad

#### No Puedo Acceder al Panel
- Verificar que el archivo `.htaccess` esté presente
- Comprobar permisos del directorio `admin/`
- Verificar configuración de Apache/Nginx

### Soporte

Para soporte técnico, contactar a:
- **Email**: tech@aramedylaboratorio.com
- **Desarrollador**: IDEAMIA Tech

---

© 2025 Aramed y Laboratorios. Todos los derechos reservados.
