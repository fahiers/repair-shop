## Plan de tareas para el módulo de órdenes de trabajo

- **Dominio y persistencia**
  - Expandir `guardarOrden` para poblar `tecnico_id`, fechas comprometidas, diagnóstico, observaciones, anticipo y saldo, garantizando que la orden refleje todo el ciclo operativo.
  - Calcular `costo_estimado` con los ítems actuales y permitir que `costo_final` se establezca al cierre; definir reglas de actualización automática del saldo en función de anticipos y pagos.
  - Validar que el cliente exista y que el dispositivo pertenezca al mismo cliente, sumado a la presencia de al menos un ítem; devolver errores descriptivos cuando falte alguno de estos requisitos.
  - Encapsular la creación de la orden y los registros pivot (`orden_servicio`, `orden_producto`) en una transacción para evitar órdenes parcialmente creadas si surge un error en medio del proceso.
  - Añadir `casts` de fechas e importes en `OrdenTrabajo`, centralizar la enumeración de estados y considerar métodos helpers (por ejemplo `isEditable`, `markAsDelivered`) para reducir duplicidad en Livewire.

- **Flujo de creación**
  - Incorporar controles visibles para habilitar/deshabilitar IVA, cambiar el porcentaje según configuraciones locales y capturar anticipos o notas del cliente directamente desde la vista.
  - Mostrar en la ficha lateral los datos críticos del cliente seleccionado (RUT, teléfono, correo) para que el recepcionista no tenga que salir del flujo.
  - Permitir crear clientes y dispositivos inline (modales rápidos) y resetear `selectedDeviceId` al cambiar de cliente para prevenir asociaciones incorrectas entre cliente y equipo.
  - Mejorar la UX de búsqueda con estados de carga claros, mensajes cuando no se encuentran coincidencias y validaciones visuales antes de cerrar modales.
  - Revisar el uso de Alpine: si Livewire puede manejar pestañas y shortcuts, simplificar la dependencia para evitar estados inconsistentes entre ambos.

- **Gestión y seguimiento**
  - Implementar el componente de edición con carga diferida de relaciones (servicios, productos, comentarios) y formularios para modificar cantidades, descuentos y técnico asignado.
  - Crear acciones específicas para cambiar el estado de la orden (diagnóstico, reparación, espera de repuesto, listo, entregado) y registrar automáticamente las fechas y usuario que realizó cada cambio.
  - Integrar el registro de pagos parciales desde la orden, descontando saldos y sincronizando con la generación de facturas o recibos, además de disparar eventos que ajusten stock cuando corresponda.

- **Adjuntos y comunicación**
  - Sustituir las secciones de fotos y notas con componentes que permitan subir evidencias (limitando tamaño, cantidad y formato) y visualizar un carrusel de archivos almacenados en disco o S3.
  - Implementar un timeline con comentarios internos y mensajes para el cliente usando `OrdenComentario`, incluyendo filtros y diferenciación visual por tipo (`nota_interna`, `comentario_cliente`).
  - Configurar notificaciones (correo/SMS/WhatsApp según disponibilidad) en los cambios de estado relevantes, usando colas y plantillas personalizadas para mantener informado al cliente.

- **Listados e insights**
  - Ajustar el listado principal para mostrar marca/modelo correctos, técnico asignado, avances de pago y saldo pendiente; añadir filtros combinables por estado, rango de fecha y técnico.
  - Incorporar navegación a una vista de detalle o modal con resumen compacto de la orden; prever acciones rápidas (cambiar estado, asignar técnico) desde la tabla cuando el usuario tenga permisos.
  - Diseñar un tablero con KPIs: total de órdenes abiertas por etapa, órdenes vencidas, anticipos sin completar, tiempo promedio en cada estado y productividad por técnico.

- **Infraestructura y QA**
  - Crear la migración para `orden_detalle_piezas` (incluyendo claves foráneas, índices y soft deletes) o eliminar el modelo si no está alineado con el alcance actual.
  - Revisar los pivotes `orden_servicio` y `orden_producto` para registrar IVA, descuentos, notas internas y referencias a stock consumido, según las necesidades del negocio.
  - Sembrar datos base: roles/permisos con Spatie, usuarios demo, modelos de dispositivos y servicios/productos frecuentes para acelerar pruebas manuales.
  - Ampliar la suite de tests incluyendo validaciones de Livewire, creación y actualización de órdenes, manejo de pagos parciales y generación secuencial de números en condiciones de carrera.
  - Mantener la documentación actualizada (`db_taller_mejorado.md`) describiendo el flujo de negocio, estados y reglas de cálculo, de modo que nuevos desarrolladores comprendan el módulo sin depender oralmente del equipo.
