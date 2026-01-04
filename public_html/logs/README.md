# Logs Directory

Este directorio contiene los archivos de logs del sistema.

## Archivos

- `php-errors.log` - Logs de errores PHP generados automáticamente

## Seguridad

Los archivos `.log` están protegidos por `.htaccess` y no son accesibles directamente desde el navegador.

Para ver los logs, usa el visor del admin:
- URL: `https://aramedylaboratorio.com/admin/view-logs.php`
- Requiere permisos de administrador

## Permisos

El archivo `php-errors.log` debe tener permisos de escritura para el servidor web:
```bash
chmod 666 php-errors.log
```

## Limpieza

Los logs se pueden limpiar desde el visor del admin o manualmente:
```bash
# Limpiar logs (mantener el archivo)
> php-errors.log

# O eliminar y recrear
rm php-errors.log
touch php-errors.log
chmod 666 php-errors.log
```

