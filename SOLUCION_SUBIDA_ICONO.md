# Solución: Error "Path must not be empty" en Livewire File Uploads

## Problema

Al intentar subir imágenes en componentes Livewire, se producía el siguiente error:

```
Path must not be empty
ValueError: Path must not be empty at FilesystemAdapter.php:466
```

Este error ocurría cuando Livewire intentaba guardar archivos temporalmente durante el proceso de subida.

## Causa

El problema estaba relacionado con la configuración de PHP. La directiva `upload_tmp_dir` en el archivo `php.ini` estaba comentada o no estaba configurada correctamente, lo que causaba que PHP no pudiera determinar dónde guardar los archivos temporales durante las subidas.

## Solución

1. Abrir el archivo `php.ini` (ubicado generalmente en `C:\laragon\bin\php\php-8.x.x\php.ini`)

2. Buscar la línea que contiene `upload_tmp_dir`

3. Descomentar la línea (eliminar el `;` al inicio) y establecer el valor:

```ini
upload_tmp_dir = "C:/laragon/tmp"
```

4. Guardar el archivo `php.ini`

5. Reiniciar el servidor web (Apache/Nginx) o Laragon

## Verificación

Para verificar que la configuración se aplicó correctamente, puedes ejecutar:

```php
php -i | findstr upload_tmp_dir
```

O crear un archivo PHP temporal:

```php
<?php
echo ini_get('upload_tmp_dir');
```

Debería mostrar: `C:/laragon/tmp`

## Notas Adicionales

- Asegúrate de que el directorio `C:/laragon/tmp` existe y tiene permisos de escritura
- Si el directorio no existe, créalo manualmente
- En sistemas Windows, usa barras diagonales `/` o barras invertidas dobles `\\` en la ruta
- Después de cambiar `php.ini`, siempre reinicia el servidor web para que los cambios surtan efecto

## Archivos Afectados

- `php.ini` - Archivo de configuración de PHP
- Componentes Livewire que manejan subida de archivos
- Configuración de Livewire en `config/livewire.php`

