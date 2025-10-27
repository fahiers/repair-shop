# Configuración de Rutas en el Proyecto DPI

## Arquitectura de Rutas Modular

Este proyecto utiliza una arquitectura de rutas modular que separa las rutas por funcionalidad en archivos individuales dentro del directorio `routes/`. La configuración principal se encuentra en `bootstrap/app.php` donde se registran todos los grupos de rutas.

## Configuración Principal en `bootstrap/app.php`

El archivo `bootstrap/app.php` utiliza el método `withRouting()` de Laravel 11 para configurar las rutas. Dentro de la función `then()`, se registran todos los grupos de rutas modulares:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        // Registro de grupos de rutas modulares
    }
)
```

## Patrón de Configuración de Módulos

Cada módulo del sistema sigue un patrón consistente:

### 1. Rutas con Prefijo y Middleware
```php
Route::prefix('nombre-modulo')
    ->middleware(['auth','web'])
    ->group(base_path('routes/archivo-modulo.php'));
```

### 2. Rutas sin Prefijo (para módulos principales)
```php
Route::middleware(['auth','web'])
    ->group(base_path('routes/archivo-modulo.php'));
```

## Estructura de Archivos de Rutas

Cada archivo de rutas en el directorio `routes/` contiene:

- **Importaciones de componentes Livewire** específicos del módulo
- **Rutas RESTful** con middleware de permisos
- **Nombres de rutas** consistentes con el patrón `modulo.accion`

### Ejemplo: `routes/clients.php`
```php


use App\Livewire\Clientes\Index as ClientesIndex;
use App\Livewire\Clientes\Create as ClientesCreate;
use App\Livewire\Clientes\Edit as ClientesEdit;
use Illuminate\Support\Facades\Route;

Route::get('/', ClientesIndex::class)
    ->name('clientes.index')
    ->middleware('permission:ver-mantencion-clientes');

Route::get('/create', ClientesCreate::class)
    ->name('clientes.create')
    ->middleware('permission:gestionar-clientes');

Route::get('/{cliente}/edit', ClientesEdit::class)
    ->name('clientes.edit')
    ->middleware('permission:gestionar-clientes');
```
## Middleware y Seguridad

### Middleware Global
- **`auth`**: Autenticación requerida
- **`web`**: Middleware web de Laravel
- **`HandlePermissionErrors`**: Middleware personalizado para manejo de errores de permisos

### Sistema de Permisos
El sistema utiliza un sistema de permisos robusto que se explica detalladamente en su guía específica. Los permisos se aplican mediante middleware en las rutas para controlar el acceso a las diferentes funcionalidades del sistema.

## Ventajas de esta Arquitectura

1. **Organización**: Cada módulo tiene sus rutas en archivos separados
2. **Mantenibilidad**: Fácil localización y modificación de rutas específicas
3. **Escalabilidad**: Simple agregar nuevos módulos
4. **Seguridad**: Middleware de permisos aplicado consistentemente
5. **Legibilidad**: Estructura clara y predecible

## Cómo Agregar un Nuevo Módulo

1. Crear archivo `routes/nuevo-modulo.php`
2. Definir rutas con middleware de permisos
3. Registrar el grupo en `bootstrap/app.php`:
```php
Route::prefix('nuevo-modulo')
    ->middleware(['auth','web'])
    ->group(base_path('routes/nuevo-modulo.php'));
```

Esta arquitectura permite un desarrollo modular y mantenible, facilitando la gestión de un sistema complejo con múltiples funcionalidades.