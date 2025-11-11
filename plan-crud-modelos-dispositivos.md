# Plan de Implementación: CRUD Modelos de Dispositivos

## Objetivo
Crear el módulo completo de CRUD para Modelos de Dispositivos siguiendo el mismo patrón y formato visual de los módulos existentes (Servicios, Productos, Clientes).

## Estructura del Módulo

### Campos del Modelo
- `marca` (string, 100) - Requerido
- `modelo` (string, 100) - Requerido
- `descripcion` (text) - Opcional
- `anio` (smallInteger) - Opcional

---

## Fase 1: Componentes Livewire

### 1.1 Componente Index (`app/Livewire/ModelosDispositivos/Index.php`)
**Funcionalidades:**
- Búsqueda en tiempo real por marca, modelo y descripción
- Paginación (10 elementos por página)
- Listado en tabla con columnas: Marca, Modelo, Descripción, Año, Acciones
- Botón "Nuevo modelo" en la parte superior
- Botón de editar en cada fila
- Mensaje cuando no hay resultados

**Campos de búsqueda:**
- Marca
- Modelo
- Descripción

**Estructura de tabla:**
- Marca
- Modelo
- Descripción (truncada)
- Año
- Acciones (botón editar)

---

### 1.2 Componente Crear (`app/Livewire/ModelosDispositivos/CrearModeloDispositivo.php`)
**Funcionalidades:**
- Formulario con validación
- Campos:
  - Marca (requerido, texto, max 100)
  - Modelo (requerido, texto, max 100)
  - Descripción (opcional, textarea)
  - Año (opcional, número, rango 1900-2030)
- Botón "Agregar modelo" con estado de carga
- Redirección a index con mensaje de éxito después de crear

**Validaciones:**
```php
'marca' => 'required|string|max:100',
'modelo' => 'required|string|max:100',
'descripcion' => 'nullable|string',
'anio' => 'nullable|integer|min:1900|max:2030',
```

---

### 1.3 Componente Editar (`app/Livewire/ModelosDispositivos/EditarModeloDispositivo.php`)
**Funcionalidades:**
- Cargar datos existentes en `mount()`
- Mismos campos que Crear
- Validaciones idénticas
- Botón "Actualizar modelo" con estado de carga
- Redirección a index con mensaje de éxito después de actualizar
- Manejo de modelo no encontrado

---

## Fase 2: Vistas Blade

### 2.1 Vista Index (`resources/views/livewire/modelos-dispositivos/index.blade.php`)
**Estructura:**
- Header con título "Modelos de Dispositivos" y botón "Nuevo modelo"
- Buscador con icono de lupa y debounce de 500ms
- Tabla responsive con:
  - Header oscuro (bg-zinc-50 dark:bg-zinc-800/50)
  - Filas con hover effect
  - Columna de acciones con icono de editar
  - Mensaje cuando no hay resultados
- Footer con información de paginación y links

**Estilos:**
- Usar clases Tailwind consistentes con otros módulos
- Soporte para dark mode
- Colores: zinc para bordes y fondos, indigo para focus

---

### 2.2 Vista Crear (`resources/views/livewire/modelos-dispositivos/crear-modelo-dispositivo.blade.php`)
**Estructura:**
- Título "Agregar un nuevo modelo de dispositivo"
- Formulario con:
  - Campo Marca (texto, requerido, autofocus)
  - Campo Modelo (texto, requerido)
  - Campo Descripción (textarea, 5 filas, opcional)
  - Campo Año (number, opcional, min 1900, max 2030)
- Mensajes de error debajo de cada campo
- Botón submit con estados de carga

**Layout:**
- Contenedor max-w-3xl centrado
- Grid de 2 columnas en desktop para Marca y Modelo
- Campo Descripción a ancho completo
- Campo Año en grid de 2 columnas (solo ocupa una)

**Estilos:**
- Inputs: border-gray-200, bg-gray-50, focus:border-indigo-500, focus:ring-indigo-500
- Botón: bg-gray-800, hover:bg-gray-900, texto blanco

---

### 2.3 Vista Editar (`resources/views/livewire/modelos-dispositivos/editar-modelo-dispositivo.blade.php`)
**Estructura:**
- Idéntica a Crear pero con título "Editar modelo de dispositivo"
- Botón "Actualizar modelo" en lugar de "Agregar modelo"
- Campos prellenados con datos existentes

---

## Fase 3: Rutas

### 3.1 Archivo de Rutas (`routes/modelos.php`)
**Rutas a crear:**
```php
Route::get('/', Index::class)
    ->name('modelos.index')
    ->middleware(['auth', 'web']);

Route::get('/crear', CrearModeloDispositivo::class)
    ->name('modelos.crear')
    ->middleware(['auth', 'web']);

Route::get('/{id}/editar', EditarModeloDispositivo::class)
    ->name('modelos.editar')
    ->middleware(['auth', 'web']);
```

**Nota:** El prefijo 'modelos' ya está configurado en `bootstrap/app.php`

---

## Fase 4: Actualización del Sidebar

### 4.1 Actualizar enlace en Sidebar (`resources/views/components/layouts/app/sidebar.blade.php`)
**Cambio necesario:**
- Cambiar `href="#"` por `route('modelos.index')` en el item de "Modelos de Dispositivos"
- Cambiar `request()->is('modelos*')` por `request()->routeIs('modelos.*')` para mejor detección de ruta activa

**Línea actual (20):**
```blade
<flux:sidebar.item icon="cpu-chip" href="#" :current="request()->is('modelos*')" wire:navigate>{{ __('Modelos de Dispositivos') }}</flux:sidebar.item>
```

**Línea nueva:**
```blade
<flux:sidebar.item icon="cpu-chip" :href="route('modelos.index')" :current="request()->routeIs('modelos.*')" wire:navigate>{{ __('Modelos de Dispositivos') }}</flux:sidebar.item>
```

---

## Fase 5: Testing (Opcional pero recomendado)

### 5.1 Tests Feature
**Archivos a crear:**
- `tests/Feature/ModelosDispositivos/IndexTest.php`
- `tests/Feature/ModelosDispositivos/CrearModeloDispositivoTest.php`
- `tests/Feature/ModelosDispositivos/EditarModeloDispositivoTest.php`

**Casos de prueba:**
- Verificar que se muestra la lista de modelos
- Verificar búsqueda funciona correctamente
- Verificar creación de modelo con datos válidos
- Verificar validaciones en creación
- Verificar edición de modelo
- Verificar validaciones en edición
- Verificar redirecciones después de crear/editar

---

## Orden de Implementación Recomendado

1. ✅ **Componente Index** (`app/Livewire/ModelosDispositivos/Index.php`)
2. ✅ **Vista Index** (`resources/views/livewire/modelos-dispositivos/index.blade.php`)
3. ✅ **Rutas** (`routes/modelos.php`)
4. ✅ **Actualizar Sidebar** (enlace funcional)
5. ✅ **Componente Crear** (`app/Livewire/ModelosDispositivos/CrearModeloDispositivo.php`)
6. ✅ **Vista Crear** (`resources/views/livewire/modelos-dispositivos/crear-modelo-dispositivo.blade.php`)
7. ✅ **Componente Editar** (`app/Livewire/ModelosDispositivos/EditarModeloDispositivo.php`)
8. ✅ **Vista Editar** (`resources/views/livewire/modelos-dispositivos/editar-modelo-dispositivo.blade.php`)
9. ✅ **Tests** (opcional)

---

## Consideraciones Adicionales

### Permisos (si aplica)
Si el sistema usa permisos, agregar middleware de permisos a las rutas:
- `permission:ver-modelos-dispositivos` para index
- `permission:gestionar-modelos-dispositivos` para crear/editar

### Soft Deletes
El modelo ya tiene SoftDeletes implementado. Considerar:
- Agregar funcionalidad de eliminación suave en el Index (opcional)
- Mostrar indicador visual si el modelo está eliminado

### Relaciones
El modelo tiene relación `dispositivos()`. Considerar:
- Mostrar contador de dispositivos asociados en el Index (opcional)
- Validar antes de eliminar si tiene dispositivos asociados

---

## Checklist Final

- [ ] Componentes Livewire creados (Index, Crear, Editar)
- [ ] Vistas Blade creadas con formato consistente
- [ ] Rutas configuradas correctamente
- [ ] Sidebar actualizado con enlace funcional
- [ ] Validaciones implementadas
- [ ] Mensajes de éxito/error funcionando
- [ ] Búsqueda y paginación funcionando
- [ ] Dark mode soportado
- [ ] Tests creados (opcional)
- [ ] Código formateado con Pint

---

## Referencias de Código Existente

**Componentes similares a seguir:**
- `app/Livewire/Servicios/Index.php` → `app/Livewire/ModelosDispositivos/Index.php`
- `app/Livewire/Servicios/CrearServicio.php` → `app/Livewire/ModelosDispositivos/CrearModeloDispositivo.php`
- `app/Livewire/Servicios/EditarServicio.php` → `app/Livewire/ModelosDispositivos/EditarModeloDispositivo.php`

**Vistas similares a seguir:**
- `resources/views/livewire/servicios/index.blade.php` → `resources/views/livewire/modelos-dispositivos/index.blade.php`
- `resources/views/livewire/servicios/crear-servicio.blade.php` → `resources/views/livewire/modelos-dispositivos/crear-modelo-dispositivo.blade.php`
- `resources/views/livewire/servicios/editar-servicio.blade.php` → `resources/views/livewire/modelos-dispositivos/editar-modelo-dispositivo.blade.php`

**Rutas similares:**
- `routes/servicios.php` → `routes/modelos.php`

