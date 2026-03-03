<?php

namespace App\Support;

class PdfDocumentMeta
{
    public static function build(string $docKey): array
    {
        $logoDataUri = null;
        $logoPathConfig = config('pdf_document.logo_path');

        if ($logoPathConfig) {
            $absolutePath = public_path($logoPathConfig);
            if (is_file($absolutePath)) {
                $content = @file_get_contents($absolutePath);
                if ($content !== false) {
                    $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
                    $mime = match ($ext) {
                        'png' => 'image/png',
                        'jpg', 'jpeg' => 'image/jpeg',
                        'svg' => 'image/svg+xml',
                        default => 'application/octet-stream',
                    };
                    $logoDataUri = 'data:'.$mime.';base64,'.base64_encode($content);
                }
            }
        }

        return [
            'institution_name' => config('pdf_document.institution_name'),
            'faculty_name' => config('pdf_document.faculty_name'),
            'address' => config('pdf_document.address'),
            'city' => config('pdf_document.city'),
            'phone' => config('pdf_document.phone'),
            'website' => config('pdf_document.website'),
            'logo_data_uri' => $logoDataUri,
            'signatures' => config("pdf_document.signatures.{$docKey}", []),
        ];
    }
}

