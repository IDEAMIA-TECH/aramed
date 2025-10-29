# Cron Jobs - Aramed y Laboratorios

## Configuración de Cron Jobs

### 1. Mensajes Topbar - Desactivación Automática

**Archivo:** `expire_topbar_messages.php`

**Descripción:** Desactiva automáticamente los mensajes del topbar que han expirado.

**Frecuencia recomendada:** Cada 15 minutos

**Comando cron:**
```bash
# Cada 15 minutos
*/15 * * * * /usr/bin/php /ruta/completa/a/public_html/cron/expire_topbar_messages.php

# O usando wget (si no tienes acceso a cron)
*/15 * * * * wget -q -O /dev/null "https://aramedylaboratorio.com/cron/expire_topbar_messages.php?cron_key=aramed_topbar_cron_2025"
```

### 2. Configuración en cPanel

1. Ir a **Cron Jobs** en cPanel
2. Agregar nuevo cron job:
   - **Minute:** `*/15`
   - **Hour:** `*`
   - **Day:** `*`
   - **Month:** `*`
   - **Weekday:** `*`
   - **Command:** `/usr/bin/php /home/usuario/public_html/cron/expire_topbar_messages.php`

### 3. Verificación Manual

Puedes ejecutar el script manualmente visitando:
```
https://aramedylaboratorio.com/cron/expire_topbar_messages.php?cron_key=aramed_topbar_cron_2025
```

### 4. Logs

Los logs se guardan en: `public_html/logs/topbar_cron.log`

### 5. Alternativas

Si no puedes configurar cron jobs, el sistema también incluye:
- **Verificación en cada carga:** Se ejecuta automáticamente cada 5 minutos cuando alguien visita el sitio
- **Botón manual:** En el admin panel hay un botón "Limpiar Expirados"
- **Evento de MySQL:** Se puede configurar un evento que se ejecute cada hora
