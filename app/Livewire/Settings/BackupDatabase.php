<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\Sqlite;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupDatabase extends Component
{
    public function descargarBackup()
    {
        // 1. Definir nombre y ruta temporal
        $fileName = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $tempPath = storage_path('app/' . $fileName);

        // 2. Obtener configuración de la base de datos
        $connection = config('database.default');

        try {
            if ($connection === 'mysql') {
                $dumper = MySql::create()
                    ->setDbName(config('database.connections.mysql.database'))
                    ->setUserName(config('database.connections.mysql.username'))
                    ->setPassword(config('database.connections.mysql.password'));
                
                // Configuración específica para entorno local Laragon si mysqldump no está en el PATH
                if (file_exists('C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe')) {
                    $dumper->setDumpBinaryPath('C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin');
                }
                
                $dumper->dumpToFile($tempPath);
            } elseif ($connection === 'sqlite') {
                Sqlite::create()
                    ->setDbName(config('database.connections.sqlite.database'))
                    ->dumpToFile($tempPath);
            } else {
                // Manejo básico para otros drivers o error
                 $this->dispatch('flux-toast', 
                    variant: 'danger', 
                    title: 'Error',
                    description: 'Motor de base de datos no soportado para descarga directa.'
                );
                return;
            }

            // 3. Descargar y eliminar el archivo temporal después del envío
            return response()
                ->download($tempPath)
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generando backup: ' . $e->getMessage());
            
            $this->dispatch('flux-toast', 
                variant: 'danger', 
                title: 'Error de Backup',
                description: 'No se pudo generar el backup: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.settings.backup-database');
    }
}

