<?php

namespace App\Livewire\Dispositivos;

use App\Models\AccesorioConfig;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\ModeloDispositivo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public Dispositivo $dispositivo;

    public $modelo_id;

    public $modelo_search = '';

    public $modelo_selected_name = '';

    public $cliente_id;

    public $cliente_search = '';

    public $cliente_selected_name = '';

    public $imei = '';

    public $color = '';

    public $estado_dispositivo = '';

    public $tipo_bloqueo = 'ninguno';

    public $contraseña = '';

    public $patron = [];

    // Accesorios configurables y seleccionados
    public array $accesoriosDisponibles = [];

    // Estructura: ['bolso' => true, 'cargador' => false, ...]
    public array $accesoriosSeleccionados = [];

    public function mount(Dispositivo $dispositivo): void
    {
        $this->dispositivo = $dispositivo;
        $this->modelo_id = $dispositivo->modelo_id;
        $this->modelo_selected_name = $dispositivo->modelo ? $dispositivo->modelo->marca.' '.$dispositivo->modelo->modelo : 'Desconocido';

        $this->cliente_id = $dispositivo->cliente_id;
        $this->cliente_selected_name = $dispositivo->cliente ? $dispositivo->cliente->nombre : 'Desconocido';

        $this->imei = $dispositivo->imei;
        $this->color = $dispositivo->color;
        $this->estado_dispositivo = $dispositivo->estado_dispositivo;

        // Cargar accesorios disponibles (activos) e inicializar selecciones
        $this->cargarAccesoriosDisponibles();
        $savedAccesorios = $dispositivo->accesorios ?? [];
        $this->inicializarAccesoriosSeleccionados((array) $savedAccesorios);

        // Load Security
        if ($dispositivo->patron || $dispositivo->pattern_encrypted) {
            $this->tipo_bloqueo = 'patron';
            // Try decrypt first, then fallback to flat string
            if ($dispositivo->pattern_encrypted) {
                try {
                    $patronString = Crypt::decryptString($dispositivo->pattern_encrypted);
                    $this->patron = array_map('intval', explode('-', $patronString));
                } catch (\Exception $e) {
                    $this->patron = [];
                }
            } elseif ($dispositivo->patron) {
                $this->patron = array_map('intval', explode('-', $dispositivo->patron));
            }
        } elseif ($dispositivo->contraseña) {
            $this->tipo_bloqueo = 'contrasena';
            $this->contraseña = $dispositivo->contraseña;
        } else {
            $this->tipo_bloqueo = 'ninguno';
        }
    }

    public function searchModelos()
    {
        if (empty($this->modelo_search)) {
            return [];
        }

        return ModeloDispositivo::where('modelo', 'like', '%'.$this->modelo_search.'%')
            ->orWhere('marca', 'like', '%'.$this->modelo_search.'%')
            ->limit(10)
            ->get();
    }

    public function selectModelo($id, $name)
    {
        $this->modelo_id = $id;
        $this->modelo_selected_name = $name;
        $this->modelo_search = '';
    }

    public function clearModelo()
    {
        $this->modelo_id = null;
        $this->modelo_selected_name = '';
        $this->modelo_search = '';
    }

    public function searchClientes()
    {
        if (empty($this->cliente_search)) {
            return [];
        }

        return Cliente::where('nombre', 'like', '%'.$this->cliente_search.'%')
            ->orWhere('rut', 'like', '%'.$this->cliente_search.'%')
            ->limit(10)
            ->get();
    }

    public function selectCliente($id, $name)
    {
        $this->cliente_id = $id;
        $this->cliente_selected_name = $name;
        $this->cliente_search = '';
    }

    public function clearCliente()
    {
        $this->cliente_id = null;
        $this->cliente_selected_name = '';
        $this->cliente_search = '';
    }

    public function save()
    {
        $this->validate([
            'modelo_id' => 'required|exists:modelos_dispositivos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'imei' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'estado_dispositivo' => 'nullable|string',
            'contraseña' => 'nullable|required_if:tipo_bloqueo,contrasena',
            'patron' => 'nullable|array|required_if:tipo_bloqueo,patron',
        ]);

        $data = [
            'modelo_id' => $this->modelo_id,
            'cliente_id' => $this->cliente_id,
            'imei' => $this->imei,
            'color' => $this->color,
            'estado_dispositivo' => $this->estado_dispositivo,
            'accesorios' => $this->accesoriosSeleccionados,
        ];

        if ($this->tipo_bloqueo === 'contrasena') {
            $data['contraseña'] = $this->contraseña;
            $data['patron'] = null;
            $data['pattern_encrypted'] = null;
        } elseif ($this->tipo_bloqueo === 'patron') {
            $patronString = implode('-', $this->patron);
            $data['patron'] = $patronString;
            $data['pattern_encrypted'] = Crypt::encryptString($patronString);
            $data['contraseña'] = null;
        } else {
            $data['contraseña'] = null;
            $data['patron'] = null;
            $data['pattern_encrypted'] = null;
        }

        $this->dispositivo->update($data);

        return redirect()->route('dispositivos.index');
    }

    protected function cargarAccesoriosDisponibles(): void
    {
        $this->accesoriosDisponibles = AccesorioConfig::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'nombre' => $acc->nombre,
                    'clave' => $this->claveAccesorio($acc->nombre),
                ];
            })
            ->toArray();
    }

    protected function inicializarAccesoriosSeleccionados(?array $desde = null): void
    {
        $map = [];
        foreach ($this->accesoriosDisponibles as $acc) {
            $clave = $acc['clave'];
            $map[$clave] = (bool) ($desde[$clave] ?? false);
        }
        if ($desde) {
            foreach ($desde as $k => $v) {
                if (! array_key_exists($k, $map)) {
                    $map[$k] = (bool) $v;
                }
            }
        }
        $this->accesoriosSeleccionados = $map;
    }

    protected function claveAccesorio(string $nombre): string
    {
        return Str::slug($nombre, '_');
    }

    public function render()
    {
        return view('livewire.dispositivos.edit', [
            'modelos' => $this->searchModelos(),
            'clientes' => $this->searchClientes(),
        ]);
    }
}
