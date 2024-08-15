<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
{
    public function ocrProcess(Request $request)
    {
        Log::info('OCR process started.');

        // Ambil file gambar dari request
        $image = $request->file('image'); // Mengambil file dari request

        if (!$image) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        // Tentukan path sementara untuk menyimpan gambar
        $filePath = storage_path('app/public') . '/' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Pindahkan file ke path sementara
        $image->move(storage_path('app/public'), basename($filePath));

        Log::info('File uploaded to: ' . $filePath);

        // Proses OCR menggunakan Tesseract
        try {
            $text = (new TesseractOCR($filePath))->run();
            Log::info('OCR Text: ' . $text);
        } catch (\Exception $e) {
            Log::error('OCR processing failed: ' . $e->getMessage());
            unlink($filePath);
            return response()->json(['error' => 'OCR processing failed'], 500);
        }

        // Hapus file gambar setelah proses OCR selesai
        unlink($filePath);

        // Kembalikan hasil dalam bentuk JSON
        return response()->json(['text' => $text], 200, ['Content-Type' => 'application/json;charset=UTF-8'], JSON_UNESCAPED_UNICODE);
    }


}
