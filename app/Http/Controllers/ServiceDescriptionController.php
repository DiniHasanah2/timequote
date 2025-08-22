<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ServiceDescriptionController extends Controller
{
    public function showFromPdf()
    {
        $parser = new Parser();

        $pdf = $parser->parseFile(storage_path('app/public/descriptions/dini.TCSv3.Calculator.v2.06.02.working.pdf'));
        $text = $pdf->getText();

        $lines = preg_split('/\n+/', $text);
        $sections = [];
        $current = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            if (preg_match('/^[A-Z][A-Za-z\s\-\(\)]+$/', $line) && strlen($line) < 70) {
                $current = $line;
                $sections[$current] = '';
            } elseif ($current) {
                $sections[$current] .= $line . "\n";
            }
        }

        return view('service_description', compact('sections'));
    }
}
