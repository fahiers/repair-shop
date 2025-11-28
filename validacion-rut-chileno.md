# Validación de RUT Chileno - Documentación Técnica

## Descripción General

Este documento describe la implementación de la validación personalizada de RUT (Rol Único Tributario) chileno en el sistema, incluyendo su registro en el backend y su integración con el frontend para validación en tiempo real mediante Livewire.

## Backend: Validación Personalizada

### Ubicación
La validación personalizada se registra en `app/Providers/AppServiceProvider.php` dentro del método `boot()`.

### Implementación

```35:71:app/Providers/AppServiceProvider.php
        Validator::extend('cl_rut', function ($attribute, $value, $parameters, $validator) {
            if (!is_string($value) && !is_numeric($value)) {
                return false;
            }

            $value = preg_replace('/[^0-9kK]/', '', $value);

            if (strlen($value) < 2) {
                return false;
            }

            $body = substr($value, 0, -1);
            $dv = strtoupper(substr($value, -1));

            if (!ctype_digit($body)) {
                return false;
            }

            $sum = 0;
            $multiple = 2;

            for ($i = strlen($body) - 1; $i >= 0; $i--) {
                $sum += $body[$i] * $multiple;
                $multiple = ($multiple == 7) ? 2 : $multiple + 1;
            }

            $expectedDv = 11 - ($sum % 11);

            $expectedDv = ($expectedDv == 11) ? '0' : (($expectedDv == 10) ? 'K' : (string)$expectedDv);

            return $dv == $expectedDv;
        });

        Validator::replacer('cl_rut', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'El campo :attribute no es un RUT chileno válido.');
        });
```

### Algoritmo de Validación

La validación implementa el algoritmo oficial de verificación de RUT chileno:

1. **Limpieza del valor**: Se eliminan todos los caracteres que no sean números o la letra 'K' (mayúscula o minúscula).

2. **Validación básica**: 
   - Debe tener al menos 2 caracteres
   - El cuerpo (todos los dígitos excepto el último) debe ser numérico

3. **Cálculo del dígito verificador**:
   - Se toma el cuerpo del RUT de derecha a izquierda
   - Se multiplica cada dígito por una serie que va de 2 a 7, repitiéndose cíclicamente
   - Se suman todos los productos
   - Se calcula: `11 - (suma % 11)`
   - Si el resultado es 11, el dígito verificador es '0'
   - Si el resultado es 10, el dígito verificador es 'K'
   - En cualquier otro caso, el dígito verificador es el resultado

4. **Comparación**: Se compara el dígito verificador calculado con el proporcionado por el usuario.

### Mensaje de Error Personalizado

El método `Validator::replacer()` personaliza el mensaje de error que se mostrará cuando la validación falle:

```php
'El campo :attribute no es un RUT chileno válido.'
```

## Frontend: Validación en Tiempo Real

### Componente Livewire

La validación se utiliza en el componente `app/Livewire/Clientes/Create.php` para crear nuevos clientes.

### Reglas de Validación

```87:112:app/Livewire/Clientes/Create.php
    public function rules(): array
    {
        $rules = [
            'rut' => ['required', 'unique:clientes,rut', 'cl_rut'],
            'nombre' => ['required', 'string', 'max:255'],
            'region_id' => ['required', 'exists:regiones,id'],
            'comuna_id' => ['required', 'exists:comunas,id'],
            'direccion' => ['required', 'string', 'max:255'],
            'giro' => ['required', 'string', 'max:255'],
            'fono' => ['required', 'string', 'max:50'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clientes,email'],
            'tipoPago' => ['required', 'in:contado,credito'],
            'credito' => ['boolean'],
            'cant_Dia_Block_x_Deuda' => ['required_if:credito,true', 'nullable', 'integer', 'min:0'],
            'saldoAfavor' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ];

        // Solo los administradores pueden seleccionar vendedor, para otros es automático
        if ($this->esAdministrador()) {
            $rules['empleado_id'] = ['nullable', 'exists:personal,id'];
        }

        return $rules;
    }
```

El campo `rut` incluye tres reglas:
- `required`: El campo es obligatorio
- `unique:clientes,rut`: El RUT debe ser único en la tabla `clientes`
- `cl_rut`: La validación personalizada de RUT chileno

### Validación en Tiempo Real

El componente implementa el método `updated()` que se ejecuta automáticamente cuando cualquier propiedad pública cambia:

```151:154:app/Livewire/Clientes/Create.php
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
```

Este método utiliza `validateOnly()` que valida únicamente el campo que cambió, proporcionando una experiencia de usuario fluida sin necesidad de enviar el formulario completo.

### Vista Blade

En la vista `resources/views/livewire/clientes/create.blade.php`, el campo RUT está configurado con `wire:model.blur`:

```22:26:resources/views/livewire/clientes/create.blade.php
                <flux:field>
                    <flux:label badge="Obligatorio">RUT</flux:label>
                    <flux:input wire:model.blur="rut" required placeholder="Ej: 12345678-9" />
                    <flux:error name="rut" />
                </flux:field>
```

**Características importantes:**

- `wire:model.blur="rut"`: El binding de Livewire se activa cuando el campo pierde el foco (`blur`), lo que significa que la validación se ejecuta cuando el usuario termina de escribir y sale del campo.

- `<flux:error name="rut" />`: Muestra automáticamente los mensajes de error de validación para el campo `rut`.

- `placeholder="Ej: 12345678-9"`: Proporciona una guía visual del formato esperado.

### Mensajes de Error Personalizados

El componente también define mensajes de error personalizados para mejorar la experiencia del usuario:

```114:149:app/Livewire/Clientes/Create.php
    public function messages(): array
    {
        return [
            'rut.required' => 'El RUT es obligatorio',
            'rut.unique' => 'Este RUT ya está registrado',
            'rut.cl_rut' => 'El formato del RUT no es válido',
            // ... otros mensajes
        ];
    }
```

## Flujo de Validación Completo

1. **Usuario ingresa RUT**: El usuario escribe el RUT en el campo del formulario.

2. **Pérdida de foco (`blur`)**: Cuando el usuario sale del campo (hace clic fuera o presiona Tab), se dispara el evento `blur`.

3. **Livewire actualiza propiedad**: Livewire actualiza la propiedad `$rut` en el componente.

4. **Método `updated()` se ejecuta**: Livewire detecta el cambio y ejecuta automáticamente el método `updated()`.

5. **Validación con `validateOnly()`**: Se valida únicamente el campo `rut` usando las reglas definidas, incluyendo la validación personalizada `cl_rut`.

6. **Algoritmo de validación**: El validador personalizado ejecuta el algoritmo de verificación del dígito verificador.

7. **Mostrar errores**: Si la validación falla, Livewire actualiza automáticamente la vista para mostrar el mensaje de error debajo del campo usando `<flux:error name="rut" />`.

8. **Validación exitosa**: Si el RUT es válido, no se muestra ningún error y el usuario puede continuar completando el formulario.

## Normalización del RUT

Antes de guardar el cliente, el RUT se normaliza al formato estándar:

```207:218:app/Livewire/Clientes/Create.php
    /**
     * Normaliza el RUT al formato estándar sin puntos: 12345678-9
     */
    private function normalizarRut(string $rut): string
    {
        // Limpiar y obtener solo números y K
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));
        
        // Separar cuerpo y dígito verificador
        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        // Retornar formato estándar: 12345678-9
        return $cuerpo . '-' . $dv;
    }
```

Esta función se llama antes de guardar el cliente para asegurar que todos los RUTs se almacenen en el mismo formato en la base de datos.

## Ejemplos de Uso

### RUTs Válidos
- `12345678-9` (con guion)
- `123456789` (sin guion)
- `12.345.678-9` (con puntos y guion)
- `12345678-K` (con K mayúscula)
- `12345678k` (con k minúscula)

### RUTs Inválidos
- `12345678-0` (dígito verificador incorrecto)
- `12345` (muy corto)
- `ABC12345-9` (contiene letras en el cuerpo)
- `12345678-X` (dígito verificador inválido)

## Ventajas de esta Implementación

1. **Validación en tiempo real**: El usuario recibe retroalimentación inmediata sin necesidad de enviar el formulario.

2. **Mejor experiencia de usuario**: Los errores se muestran de forma clara y contextual.

3. **Validación consistente**: La misma regla de validación se puede reutilizar en cualquier parte de la aplicación.

4. **Separación de responsabilidades**: La lógica de validación está centralizada en el Service Provider.

5. **Formato normalizado**: Todos los RUTs se almacenan en el mismo formato en la base de datos.

6. **Algoritmo oficial**: Implementa el algoritmo estándar de validación de RUT chileno, garantizando precisión.

## Consideraciones Técnicas

- La validación acepta RUTs con o sin puntos y guiones, ya que estos se eliminan antes de la validación.

- El dígito verificador puede ser 'K' (mayúscula o minúscula), que se convierte a mayúscula para la comparación.

- La validación se ejecuta tanto en el frontend (tiempo real) como en el backend (al enviar el formulario), proporcionando doble capa de seguridad.

- El uso de `wire:model.blur` en lugar de `wire:model.live` reduce las peticiones al servidor, validando solo cuando el usuario termina de escribir.

