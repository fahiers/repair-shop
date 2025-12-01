<?php

namespace App\Console\Commands;

use App\Models\ModeloDispositivo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportarModelosDispositivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modelos:importar 
                            {--file= : Procesar solo un archivo específico}
                            {--force : Forzar reimportación eliminando duplicados existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar modelos de dispositivos desde archivos JSON';

    /**
     * Estadísticas de la importación
     */
    private int $totalArchivos = 0;

    private int $archivosExitosos = 0;

    private int $archivosFallidos = 0;

    private int $modelosInsertados = 0;

    private int $modelosDuplicados = 0;

    private array $archivosConErrores = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando importación de modelos de dispositivos...');
        $this->newLine();

        $jsonPath = base_path('json-dispositivos');

        if (! File::exists($jsonPath)) {
            $this->error("El directorio {$jsonPath} no existe.");

            return Command::FAILURE;
        }

        // Obtener archivos JSON a procesar
        $archivos = $this->obtenerArchivosJson($jsonPath);

        if (empty($archivos)) {
            $this->warn('No se encontraron archivos JSON para procesar.');

            return Command::FAILURE;
        }

        $this->totalArchivos = count($archivos);
        $this->info("Se encontraron {$this->totalArchivos} archivos JSON para procesar.");
        $this->newLine();

        // Procesar cada archivo
        $bar = $this->output->createProgressBar($this->totalArchivos);
        $bar->start();

        foreach ($archivos as $archivo) {
            try {
                $this->procesarArchivo($archivo);
                $this->archivosExitosos++;
            } catch (\Exception $e) {
                $this->archivosFallidos++;
                $this->archivosConErrores[] = [
                    'archivo' => basename($archivo),
                    'error' => $e->getMessage(),
                ];
                $this->warn("\nError procesando {$archivo}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->mostrarResumen();

        return $this->archivosFallidos > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Obtener lista de archivos JSON a procesar
     */
    private function obtenerArchivosJson(string $path): array
    {
        $archivoEspecifico = $this->option('file');

        if ($archivoEspecifico) {
            if (File::exists($archivoEspecifico)) {
                $rutaCompleta = $archivoEspecifico;
            } elseif (File::exists("{$path}/{$archivoEspecifico}")) {
                $rutaCompleta = "{$path}/{$archivoEspecifico}";
            } else {
                $rutaCompleta = null;
            }

            if (! $rutaCompleta || ! File::exists($rutaCompleta)) {
                $this->error("El archivo {$archivoEspecifico} no existe.");

                return [];
            }

            return [$rutaCompleta];
        }

        // Buscar todos los archivos gsm_*.json
        return glob("{$path}/gsm_*.json");
    }

    /**
     * Procesar un archivo JSON
     */
    private function procesarArchivo(string $archivo): void
    {
        $contenido = File::get($archivo);
        $datos = json_decode($contenido, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON inválido: '.json_last_error_msg());
        }

        if (! isset($datos['marca'])) {
            throw new \Exception('El archivo no contiene el campo "marca"');
        }

        if (! isset($datos['modelos']) || ! is_array($datos['modelos'])) {
            throw new \Exception('El archivo no contiene un array "modelos" válido');
        }

        $marca = trim($datos['marca']);
        $modelos = $datos['modelos'];

        if (empty($marca)) {
            throw new \Exception('La marca está vacía');
        }

        if (empty($modelos)) {
            // Archivo sin modelos, no es un error, solo continuar
            return;
        }

        // Procesar modelos en transacción
        DB::transaction(function () use ($marca, $modelos) {
            foreach ($modelos as $modeloData) {
                if (! isset($modeloData['modelo'])) {
                    continue; // Saltar si no tiene modelo
                }

                $modelo = trim($modeloData['modelo']);
                if (empty($modelo)) {
                    continue; // Saltar si el modelo está vacío
                }

                // Obtener año si existe
                $anio = null;
                if (isset($modeloData['ano'])) {
                    $ano = $modeloData['ano'];
                    // Validar que sea numérico
                    if (is_numeric($ano)) {
                        $anio = (int) $ano;
                    }
                }

                // Buscar o crear el modelo (evitar duplicados)
                $existe = ModeloDispositivo::query()
                    ->whereRaw('LOWER(marca) = ?', [mb_strtolower($marca)])
                    ->whereRaw('LOWER(modelo) = ?', [mb_strtolower($modelo)])
                    ->where(function ($query) use ($anio) {
                        if ($anio !== null) {
                            $query->where('anio', $anio);
                        } else {
                            $query->whereNull('anio');
                        }
                    })
                    ->first();

                if ($existe) {
                    $this->modelosDuplicados++;
                } else {
                    ModeloDispositivo::create([
                        'marca' => $marca,
                        'modelo' => $modelo,
                        'anio' => $anio,
                        'descripcion' => null,
                    ]);
                    $this->modelosInsertados++;
                }
            }
        });
    }

    /**
     * Mostrar resumen de la importación
     */
    private function mostrarResumen(): void
    {
        $this->info('=== RESUMEN DE IMPORTACIÓN ===');
        $this->newLine();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de archivos procesados', $this->totalArchivos],
                ['Archivos exitosos', $this->archivosExitosos],
                ['Archivos con errores', $this->archivosFallidos],
                ['Modelos insertados', $this->modelosInsertados],
                ['Modelos duplicados (ya existían)', $this->modelosDuplicados],
            ]
        );

        if (! empty($this->archivosConErrores)) {
            $this->newLine();
            $this->error('Archivos con errores:');
            $this->newLine();

            $errores = array_map(function ($error) {
                return [$error['archivo'], $error['error']];
            }, $this->archivosConErrores);

            $this->table(['Archivo', 'Error'], $errores);
        }

        $this->newLine();
    }
}
