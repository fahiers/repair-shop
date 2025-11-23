# Configuración de Base de Datos - Ambientes del Proyecto

## Arquitectura de Ambientes

Este proyecto utiliza **diferentes motores de base de datos** según el ambiente:

- **Rama `dev` (Local)**: **MySQL** - Entorno de desarrollo local
- **Rama `main` (Producción - Railway)**: **PostgreSQL** - Entorno de producción desplegado

## Diferencia Crítica: Case Sensitivity en Búsquedas

### MySQL vs PostgreSQL con Búsquedas de Texto

**MySQL (Local/Dev)**:
- El operador `LIKE` es **case-insensitive** por defecto
- `WHERE nombre LIKE '%iphone%'` encuentra "Iphone", "IPHONE", "iPhone", etc.

**PostgreSQL (Producción/Main)**:
- El operador `LIKE` es **case-sensitive** estrictamente
- `WHERE nombre LIKE '%iphone%'` **SOLO** encuentra exactamente "iphone" (minúsculas)
- PostgreSQL tiene el operador `ILIKE` que es **case-insensitive** nativo y optimizado

### Solución: Detectar Motor de Base de Datos

Para que las búsquedas funcionen correctamente en **ambos ambientes**, usar:
- **`LIKE`** cuando el motor sea **MySQL** (dev/local)
- **`ILIKE`** cuando el motor sea **PostgreSQL** (main/producción)

### Implementación

```php
use Illuminate\Support\Facades\DB;

// Detectar el driver de la base de datos
$operador = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

// Usar en las consultas
->where('nombre', $operador, '%'.$valor.'%')
```

### Ejemplo de Implementación Correcta

```php
use Illuminate\Support\Facades\DB;

public function updatedBusquedaItem($valor): void
{
    if (strlen($valor) < 2) {
        $this->itemsDisponibles = [];
        return;
    }

    // Determinar operador según el motor de BD
    $operador = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

    if ($this->tipoItemAgregar === 'servicio') {
        $this->itemsDisponibles = Servicio::query()
            ->where('estado', 'activo')
            ->where('nombre', $operador, '%'.$valor.'%')
            ->limit(10)
            ->get()
            ->toArray();
    } else {
        $this->itemsDisponibles = Producto::query()
            ->where('estado', 'activo')
            ->where(function ($query) use ($valor, $operador) {
                $query->where('nombre', $operador, '%'.$valor.'%')
                    ->orWhere('marca', $operador, '%'.$valor.'%');
            })
            ->limit(10)
            ->get()
            ->toArray();
    }
}
```

## Otras Diferencias Comunes MySQL vs PostgreSQL

### 1. Truncamiento de Strings

- **MySQL**: Por defecto puede truncar strings largos (modo no estricto)
- **PostgreSQL**: Error si intentas insertar string más largo que la columna (más estricto)

### 2. Operadores de Comparación

- Ambos soportan `=`, `!=`, `LIKE`, `IN`, `BETWEEN`
- PostgreSQL tiene `ILIKE` (case-insensitive) que se usa solo en producción

### 3. Escapado de Strings

- Ambos manejan caracteres especiales, pero PostgreSQL es más estricto con comillas

## Buenas Prácticas

### ✅ Hacer

1. **Siempre detectar el driver antes de usar operadores específicos**:
   ```php
   $operador = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
   ```

2. **Probar localmente con MySQL** y **verificar en producción con PostgreSQL** antes de hacer merge a `main`

3. **Usar consultas Eloquent estándar** siempre que sea posible (Laravel abstrae muchas diferencias)

4. **Validar longitud de strings** antes de insertar para evitar errores en PostgreSQL

### ❌ Evitar

1. **NO usar `ILIKE` directamente** sin verificar el driver (no funciona en MySQL)

2. **NO usar `LIKE` asumiendo case-insensitivity** en PostgreSQL (no funciona igual)

3. **NO asumir comportamientos específicos de MySQL** que no funcionen en PostgreSQL

## Casos Específicos del Proyecto

### Búsqueda de Servicios y Productos

Las búsquedas en los componentes `CrearOrden` y `EditarOrden` **DEBEN** detectar el driver y usar `ILIKE` para PostgreSQL y `LIKE` para MySQL.

**Archivos afectados**:
- `app/Livewire/OrdenesTrabajo/CrearOrden.php` - método `updatedBusquedaItem()`
- `app/Livewire/OrdenesTrabajo/EditarOrden.php` - método `updatedBusquedaItem()`

### Búsqueda de Clientes

La búsqueda de clientes también debería revisarse para consistencia, aunque puede funcionar porque MySQL es más permisivo.

## Verificación Post-Deploy

Cuando se despliega en Railway (PostgreSQL), verificar:

1. ✅ Búsquedas con texto en minúsculas encuentran resultados con mayúsculas
2. ✅ Búsquedas parciales funcionan correctamente
3. ✅ No hay errores de SQL en los logs de Railway
4. ✅ Las consultas complejas retornan resultados esperados

## Configuración de Ambiente

- **Local (.env)**: `DB_CONNECTION=mysql`
- **Railway (Variables de Entorno)**: `DB_CONNECTION=pgsql`

Laravel detecta automáticamente el driver y aplica las configuraciones correspondientes en `config/database.php`.

