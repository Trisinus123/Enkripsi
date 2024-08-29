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

        session()->flash('message', 'Starting encryption process...');
        
        $encryptedContent = $this->encryptContent($fileContent, $publicKeyPath);

        session()->flash('message', 'File encrypted successfully.');

        // Store the encrypted content in the database
        $encryptedFile = EncryptedFile::create([
            'original_filename' => $originalFilename,
            'encrypted_content' => base64_encode($encryptedContent),
        ]);

        session()->flash('success', 'File encrypted and stored in the database.');
        
        return redirect()->route('form');
    }

    public function decrypt($id)
    {
        // Step 1: Retrieve the encrypted file record from the database
        $encryptedFile = EncryptedFile::findOrFail($id);

        // Step 2: Specify the path to the private key used for decryption
        $privateKeyPath = storage_path('keys/private_key.pem');

        // Step 3: Flash a message to the session indicating the start of the decryption process
        session()->flash('message', 'Starting decryption process...');

        // Step 4: Decrypt the content using the private key
        try {
            $decryptedContent = $this->decryptContent(base64_decode($encryptedFile->encrypted_content), $privateKeyPath);

            // Step 5: Flash a message to the session indicating that the decryption was successful
            session()->flash('message', 'File decrypted successfully.');

            // Step 6: Store the decrypted file temporarily in a directory named 'decrypted'
            $decryptedFilePath = 'decrypted/' . $encryptedFile->original_filename;
            Storage::put($decryptedFilePath, $decryptedContent);

            // Step 7: Flash a success message to the session indicating that the file is ready for download
            session()->flash('success', 'File decrypted and ready for download.');

            // Step 8: Return the decrypted file to the user as a downloadable response
            return Storage::download($decryptedFilePath);
        } catch (\Exception $e) {
            // Flash an error message if something goes wrong
            session()->flash('error', 'Decryption failed: ' . $e->getMessage());
            return redirect()->route('form');
        }
    }

    public function downloadEncrypted($id)
    {
        $encryptedFile = EncryptedFile::findOrFail($id);

        // Generate a temporary path for the encrypted file
        $encryptedFilePath = 'encrypted/' . $encryptedFile->original_filename;
        Storage::put($encryptedFilePath, base64_decode($encryptedFile->encrypted_content));

        // Return the encrypted file for download
        return Storage::download($encryptedFilePath);
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
        $decryptedContent = '';

        if (openssl_private_decrypt($encryptedContent, $decryptedContent, $privateKey)) {
            return $decryptedContent;
        } else {
            throw new \Exception('Decryption failed.');
        }
    }

}
