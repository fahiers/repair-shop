<?php

if (! function_exists('get_excel_constant')) {
    /**
     * Obtiene una constante de Excel de forma segura durante composer update
     */
    function get_excel_constant(string $constant, string $fallback): string
    {
        if (! class_exists(\Maatwebsite\Excel\Excel::class)) {
            return $fallback;
        }

        try {
            $constantName = \Maatwebsite\Excel\Excel::class.'::'.$constant;
            $value = constant($constantName);
            return $value !== false ? $value : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }
}

if (! function_exists('get_excel_class')) {
    /**
     * Obtiene el nombre de una clase de Excel de forma segura durante composer update
     */
    function get_excel_class(string $className): ?string
    {
        $fullClassName = "Maatwebsite\\Excel\\{$className}";
        
        return class_exists($fullClassName) ? $fullClassName : null;
    }
}

return [
    'exports' => [
        'chunk_size' => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],
        'properties' => [
            'creator' => '',
            'lastModifiedBy' => '',
            'title' => '',
            'description' => '',
            'subject' => '',
            'keywords' => '',
            'category' => '',
            'manager' => '',
            'company' => '',
        ],
    ],

    'imports' => [
        'read_only' => true,
        'ignore_empty' => false,
        'heading_row' => [
            'formatter' => 'slug',
        ],
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],
        'properties' => [
            'creator' => '',
            'lastModifiedBy' => '',
            'title' => '',
            'description' => '',
            'subject' => '',
            'keywords' => '',
            'category' => '',
            'manager' => '',
            'company' => '',
        ],
    ],

    'extension_detector' => [
        'xlsx' => get_excel_constant('XLSX', 'Xlsx'),
        'xlsm' => get_excel_constant('XLSX', 'Xlsx'),
        'xltx' => get_excel_constant('XLSX', 'Xlsx'),
        'xltm' => get_excel_constant('XLSX', 'Xlsx'),
        'xls' => get_excel_constant('XLS', 'Xls'),
        'xlt' => get_excel_constant('XLS', 'Xls'),
        'ods' => get_excel_constant('ODS', 'Ods'),
        'ots' => get_excel_constant('ODS', 'Ods'),
        'slk' => get_excel_constant('SLK', 'Slk'),
        'xml' => get_excel_constant('XML', 'Xml'),
        'gnumeric' => get_excel_constant('GNUMERIC', 'Gnumeric'),
        'htm' => get_excel_constant('HTML', 'Html'),
        'html' => get_excel_constant('HTML', 'Html'),
        'csv' => get_excel_constant('CSV', 'Csv'),
        'tsv' => get_excel_constant('TSV', 'Tsv'),
        'pdf' => get_excel_constant('DOMPDF', 'Dompdf'),
    ],

    'value_binder' => [
        'default' => get_excel_class('DefaultValueBinder') ?: 'Maatwebsite\\Excel\\DefaultValueBinder',
    ],

    'cache' => [
        'driver' => 'memory',
        'batch' => [
            'memory_limit' => 60000,
        ],
        'illuminate' => [
            'store' => null,
        ],
    ],

    'transactions' => [
        'handler' => 'null',
        'db'      => [
            'connection' => null,
        ],
    ],

    'temporary_files' => [
        'local_path' => storage_path('app'),
        'remote_disk' => null,
        'remote_prefix' => null,
        'force_resync_remote' => null,
    ],
];

