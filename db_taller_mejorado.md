# 📱 Base de Datos — Sistema de Taller de Reparación de Móviles (v3)

## 🔖 Descripción general
Sistema de gestión de un **taller técnico de reparación de dispositivos móviles** desarrollado en **Laravel + Livewire**.

Incluye módulos para:
- Clientes  
- Catálogo de modelos de dispositivos  
- Dispositivos reales (propiedad del cliente)  
- Órdenes de trabajo  
- Piezas / repuestos  
- Facturación con pagos parciales
- Seguimiento técnico y comentarios  
- Usuarios con roles

---

## 🧩 Entidades y estructura

### 1. 🧍‍♂️ clientes
Información de los clientes.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| nombre | VARCHAR(191) | Nombre completo |
| telefono | VARCHAR(30) | Teléfono principal |
| email | VARCHAR(191) NULL | Correo electrónico |
| direccion | VARCHAR(255) NULL | Dirección |
| rut | VARCHAR(20) NULL | RUT o documento |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Índices:**
```sql
INDEX(telefono)
INDEX(rut)
INDEX(email)
```

---

### 2. 📱 modelos_dispositivos
Catálogo general de modelos de teléfonos y tablets.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| marca | VARCHAR(100) | Ej: Samsung, Apple |
| modelo | VARCHAR(100) | Ej: A52, iPhone 13 |
| descripcion | TEXT NULL | Detalles opcionales |
| anio | SMALLINT NULL | Año de lanzamiento |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Índices:**
```sql
INDEX(marca, modelo)
```

---

### 3. 🔧 dispositivos
Dispositivos reales registrados en el taller.  
Pueden estar asociados a un cliente o no.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| cliente_id | BIGINT NULL | FK → clientes.id |
| modelo_id | BIGINT | FK → modelos_dispositivos.id |
| imei | VARCHAR(50) NULL | Identificador único |
| color | VARCHAR(50) NULL | |
| observaciones | TEXT NULL | |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

📌 `cliente_id` es **nullable** para permitir tener dispositivos "genéricos" o de catálogo.

**Índices:**
```sql
INDEX(imei)
INDEX(cliente_id, modelo_id)
```

---

### 4. 🧾 ordenes_trabajo
Cada orden representa una reparación o servicio técnico.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| numero_orden | VARCHAR(50) UNIQUE | Ej: OT-0001 |
| dispositivo_id | BIGINT | FK → dispositivos.id |
| tecnico_id | BIGINT NULL | FK → users.id |
| fecha_ingreso | DATE | |
| fecha_entrega_estimada | DATE NULL | |
| fecha_entrega_real | DATE NULL | |
| problema_reportado | TEXT | Descripción del cliente |
| diagnostico | TEXT NULL | Análisis del técnico |
| estado | ENUM('pendiente','diagnostico','en_reparacion','espera_repuesto','listo','entregado','cancelado') | Estado del proceso |
| costo_estimado | DECIMAL(10,2) NULL | Estimación inicial |
| costo_final | DECIMAL(10,2) NULL | Total final |
| anticipo | DECIMAL(10,2) DEFAULT 0 | Señas pagadas |
| saldo | DECIMAL(10,2) DEFAULT 0 | Pendiente de pago |
| observaciones | TEXT NULL | Notas varias |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Índices:**
```sql
UNIQUE(numero_orden)
INDEX(estado)
INDEX(fecha_ingreso)
INDEX(dispositivo_id, estado)
INDEX(tecnico_id)
```

**Validaciones:**
```sql
CHECK (fecha_entrega_estimada >= fecha_ingreso OR fecha_entrega_estimada IS NULL)
CHECK (costo_final >= 0 OR costo_final IS NULL)
CHECK (anticipo >= 0)
CHECK (saldo >= 0)
```

---

### 5. ⚙️ piezas
Inventario de piezas y repuestos.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| nombre | VARCHAR(191) | Ej: Pantalla iPhone 11 |
| descripcion | TEXT NULL | |
| stock | INT DEFAULT 0 | |
| precio_compra | DECIMAL(10,2) | |
| precio_venta | DECIMAL(10,2) | |
| proveedor | VARCHAR(191) NULL | |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Índices:**
```sql
INDEX(nombre)
INDEX(stock)
```

**Validaciones:**
```sql
CHECK (stock >= 0)
CHECK (precio_venta >= precio_compra)
CHECK (precio_compra >= 0)
```

---

### 6. 🧩 orden_detalle_piezas
Piezas utilizadas en una orden (relación N:N entre órdenes y piezas).

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| orden_id | BIGINT | FK → ordenes_trabajo.id |
| pieza_id | BIGINT | FK → piezas.id |
| cantidad | INT | |
| precio_unitario | DECIMAL(10,2) | |
| subtotal | DECIMAL(10,2) | cantidad × precio_unitario |
| created_at / updated_at | TIMESTAMP | |

**Índices:**
```sql
INDEX(orden_id)
INDEX(pieza_id)
```

**Validaciones:**
```sql
CHECK (cantidad > 0)
CHECK (precio_unitario >= 0)
CHECK (subtotal >= 0)
```

---

### 7. 💬 orden_comentarios
Notas internas o comentarios visibles al cliente.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| orden_id | BIGINT | FK → ordenes_trabajo.id |
| user_id | BIGINT | FK → users.id |
| comentario | TEXT | Contenido del mensaje |
| tipo | ENUM('nota_interna','comentario_cliente') | Clasificación |
| created_at / updated_at | TIMESTAMP | |

**Índices:**
```sql
INDEX(orden_id)
INDEX(tipo)
```

---

### 8. 💰 facturas
Registra las facturas asociadas a las órdenes.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| orden_id | BIGINT | FK → ordenes_trabajo.id |
| numero_factura | VARCHAR(50) UNIQUE | Ej: F-000123 |
| fecha | DATE | |
| monto_total | DECIMAL(10,2) | |
| metodo_pago | ENUM('efectivo','tarjeta','transferencia','otros') | |
| estado | ENUM('pendiente','pagado','pagado_parcial','anulado') | |
| created_at / updated_at | TIMESTAMP | |

**Índices:**
```sql
UNIQUE(numero_factura)
INDEX(orden_id)
INDEX(estado)
INDEX(fecha)
```

---

### 9. 💳 factura_pagos (NUEVO)
Pagos parciales o totales de una factura.

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| factura_id | BIGINT | FK → facturas.id |
| fecha_pago | DATE | |
| monto | DECIMAL(10,2) | |
| metodo_pago | ENUM('efectivo','tarjeta','transferencia','otros') | |
| referencia | VARCHAR(100) NULL | Nº transacción/comprobante |
| notas | TEXT NULL | |
| created_at / updated_at | TIMESTAMP | |

**Índices:**
```sql
INDEX(factura_id)
INDEX(fecha_pago)
```

**Validaciones:**
```sql
CHECK (monto > 0)
```

---

### 10. 👤 users
Usuarios del sistema (tabla estándar de Laravel).

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT | PK |
| name | VARCHAR(191) | Nombre |
| email | VARCHAR(191) UNIQUE | |
| password | VARCHAR(191) | |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

**Índices:**
```sql
UNIQUE(email)
```

📌 **Nota:** Los roles se manejarán con [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)

---

## 🔗 Relaciones principales (Eloquent)

```php
// Cliente.php
public function dispositivos() { 
    return $this->hasMany(Dispositivo::class); 
}

// ModeloDispositivo.php
public function dispositivos() { 
    return $this->hasMany(Dispositivo::class, 'modelo_id'); 
}

// Dispositivo.php
public function cliente() { 
    return $this->belongsTo(Cliente::class); 
}
public function modelo() { 
    return $this->belongsTo(ModeloDispositivo::class, 'modelo_id'); 
}
public function ordenes() { 
    return $this->hasMany(OrdenTrabajo::class); 
}

// OrdenTrabajo.php
public function dispositivo() { 
    return $this->belongsTo(Dispositivo::class); 
}
public function tecnico() {
    return $this->belongsTo(User::class, 'tecnico_id');
}
public function piezas() { 
    return $this->belongsToMany(Pieza::class, 'orden_detalle_piezas')
                ->withPivot('cantidad', 'precio_unitario', 'subtotal')
                ->withTimestamps();
}
public function comentarios() { 
    return $this->hasMany(OrdenComentario::class, 'orden_id'); 
}
public function factura() { 
    return $this->hasOne(Factura::class); 
}

// Pieza.php
public function ordenes() { 
    return $this->belongsToMany(OrdenTrabajo::class, 'orden_detalle_piezas')
                ->withPivot('cantidad', 'precio_unitario', 'subtotal');
}

// Factura.php
public function orden() {
    return $this->belongsTo(OrdenTrabajo::class, 'orden_id');
}
public function pagos() {
    return $this->hasMany(FacturaPago::class, 'factura_id');
}

// FacturaPago.php (NUEVO)
public function factura() {
    return $this->belongsTo(Factura::class, 'factura_id');
}

// User.php
public function ordenesAsignadas() {
    return $this->hasMany(OrdenTrabajo::class, 'tecnico_id');
}
public function comentarios() {
    return $this->hasMany(OrdenComentario::class, 'user_id');
}
```

---


## 🧠 Consideraciones adicionales

### Roles y Permisos con Spatie

```bash
# Instalación
composer require spatie/laravel-permission

# Publicar configuración
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**Roles sugeridos:**
- `admin`: Acceso total
- `tecnico`: Gestión de órdenes asignadas
- `recepcion`: Registro de clientes y órdenes

**Permisos sugeridos:**
```php
// Clientes
'ver_clientes', 'crear_clientes', 'editar_clientes', 'eliminar_clientes'

// Órdenes
'ver_ordenes', 'crear_ordenes', 'editar_ordenes', 'eliminar_ordenes', 'asignar_tecnico'

// Piezas
'ver_piezas', 'crear_piezas', 'editar_piezas', 'eliminar_piezas', 'ajustar_stock'

// Facturas
'ver_facturas', 'crear_facturas', 'editar_facturas', 'anular_facturas', 'registrar_pagos'
```

```

### Soft Deletes - Uso en Modelos

```php
// En todos los modelos principales
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}

// Consultas
Cliente::all(); // Solo activos
Cliente::withTrashed()->get(); // Incluye eliminados
Cliente::onlyTrashed()->get(); // Solo eliminados

// Restaurar
$cliente->restore();

// Eliminar permanentemente
$cliente->forceDelete();
```

### Estados de Orden - Lógica

- **pendiente**: Recién ingresado
- **diagnostico**: En evaluación técnica
- **en_reparacion**: Trabajo en progreso
- **espera_repuesto**: Detenido por falta de pieza
- **listo**: Reparado, listo para entrega
- **entregado**: Cliente retiró el dispositivo
- **cancelado**: Cliente canceló la orden

---

## 📦 Próximos Pasos

1. **Generar Migraciones**
   ```bash
   php artisan make:migration create_sistema_taller_tables
   ```

2. **Crear Modelos con Relaciones**
   ```bash
   php artisan make:model Cliente
   php artisan make:model OrdenTrabajo
   # etc...
   ```

3. **Seeders Básicos**
   - Usuarios admin/técnico/recepción
   - Modelos de dispositivos populares
   - Estados y datos de prueba

4. **Livewire Components**
   - Gestión de órdenes
   - Registro de pagos
   - Búsqueda de dispositivos

---

**Versión:** v3 — Simplificada y Mejorada  
**Fecha:** Octubre 2025  

**Cambios clave:**
- ✅ Tabla `factura_pagos` para pagos parciales
- ✅ Soft deletes en todas las tablas
- ✅ Campos `anticipo` y `saldo` en órdenes
- ✅ Índices optimizados
- ✅ Validaciones robustas
- ✅ Estado `cancelado` en órdenes
- ✅ Estado `pagado_parcial` en facturas

**Estructura:** 10 tablas principales + relaciones completas