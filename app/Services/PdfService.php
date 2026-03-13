<?php

declare(strict_types=1);

namespace App\Services;

class PdfService
{
    public function generate(string $title, string $html): string
    {
        // Punto de extensión para Dompdf/TCPDF.
        // Retorna la ruta de archivo temporal generado.
        $path = BASE_PATH . '/public/assets/pdf/' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($title)) . '.html';
        file_put_contents($path, $html);
        return $path;
    }
}
