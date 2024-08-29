<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Encryption</title>
</head>
<body>
    <h1>Encrypt and Decrypt File</h1>

    <!-- Form for file upload -->
    <form action="{{ route('encrypt') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Choose a file to encrypt:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Encrypt</button>
    </form>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <h2>Encrypted Files</h2>
    <ul>
        @foreach($files as $file)
            <li>
                {{ $file->original_filename }}
                <form action="{{ route('decrypt', $file->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Decrypt & Download</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
