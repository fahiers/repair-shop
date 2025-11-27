# Configuración para Railway

Este proyecto requiere la extensión PHP `intl` para funcionar correctamente, ya que utiliza el método `Number::currency()` de Laravel.

## Solución: Variable de Entorno (Recomendada)

La forma más sencilla y confiable de habilitar la extensión `intl` en Railway es configurar la variable de entorno:

1. Ve a tu proyecto en Railway
2. Navega a la sección **Variables** o **Environment Variables**
3. Agrega una nueva variable:
   - **Nombre**: `RAILPACK_PHP_EXTENSIONS`
   - **Valor**: `intl`
4. Guarda los cambios y vuelve a desplegar

**Nota**: Si necesitas múltiples extensiones, sepáralas con comas: `intl,zip,gd`

## Configuraciones Incluidas

El proyecto ya incluye las siguientes configuraciones para facilitar el despliegue:

1. **`composer.json`**: Incluye `ext-intl` en la sección `require` y `config.platform` para que Composer valide la extensión
2. **`railway.json`**: Configuración básica de Railway con Railpack como builder

## Verificación

Después del despliegue, puedes verificar que la extensión está instalada ejecutando en el terminal de Railway:

```bash
php -m | grep intl
```

O desde el código PHP:

```php
var_dump(extension_loaded('intl'));
```

## Solución de Problemas

Si el error persiste después de configurar la variable de entorno:

1. Verifica que la variable `RAILPACK_PHP_EXTENSIONS` esté configurada correctamente
2. Revisa los logs de construcción en Railway para ver si hay errores durante la instalación
3. Asegúrate de que todas las demás variables de entorno necesarias estén configuradas
4. Intenta hacer un despliegue limpio (elimina el servicio y créalo de nuevo)

## Variables de Entorno Necesarias

Además de `RAILPACK_PHP_EXTENSIONS`, asegúrate de configurar:

- `APP_KEY`: Genera con `php artisan key:generate`
- `APP_ENV`: `production`
- `APP_DEBUG`: `false`
- `DB_*`: Variables de conexión a la base de datos
- Y cualquier otra variable específica de tu aplicación

