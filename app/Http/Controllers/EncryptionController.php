<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EncryptedFile;
use Illuminate\Support\Facades\Storage;

class EncryptionController extends Controller
{
    public function showForm()
    {
        $files = EncryptedFile::all();
        return view('encrypt', compact('files'));
    }

    public function encrypt(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        // Handle the uploaded file
        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $fileContent = $file->get();

        $publicKeyPath = storage_path('keys/public_key.pem');
        $encryptedContent = $this->encryptContent($fileContent, $publicKeyPath);

        // Store the encrypted content in the database
        $encryptedFile = EncryptedFile::create([
            'original_filename' => $originalFilename,
            'encrypted_content' => base64_encode($encryptedContent),
        ]);

        return redirect()->route('form')->with('success', 'File encrypted and stored in the database.');
    }

    public function decrypt($id)
    {
        $encryptedFile = EncryptedFile::findOrFail($id);

        $privateKeyPath = storage_path('keys/private_key.pem');
        $decryptedContent = $this->decryptContent(base64_decode($encryptedFile->encrypted_content), $privateKeyPath);

        // Store the decrypted file temporarily
        $decryptedFilePath = 'decrypted/' . $encryptedFile->original_filename;
        Storage::put($decryptedFilePath, $decryptedContent);

        return Storage::download($decryptedFilePath);
    }

    private function encryptContent($content, $publicKeyPath)
    {
        $publicKey = file_get_contents($publicKeyPath);
        openssl_public_encrypt($content, $encryptedContent, $publicKey);
        return $encryptedContent;
    }

    private function decryptContent($encryptedContent, $privateKeyPath)
    {
        $privateKey = file_get_contents($privateKeyPath);
        openssl_private_decrypt($encryptedContent, $decryptedContent, $privateKey);
        return $decryptedContent;
    }
}
