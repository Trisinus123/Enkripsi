@extends('layouts.app')

@section('content')
<div class="container">
    <h1>File Encryption</h1>

    <!-- Flash Messages for Process Feedback -->
    @if (session('message'))
        <div class="alert alert-info">
            {{ session('message') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('encrypt') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Upload File to Encrypt:</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Encrypt and Upload</button>
    </form>

    <h2 class="mt-5">Encrypted Files</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Original Filename</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
                <tr>
                    <td>{{ $file->original_filename }}</td>
                    <td>
                        <!-- Decrypt Button -->
                        <form action="{{ route('decrypt', $file->id) }}" method="GET" style="display:inline;">
                            <button type="submit" class="btn btn-success">Download Decrypted</button>
                        </form>

<<<<<<< HEAD
                        <!-- Download Encrypted Button -->
                        <form action="{{ route('downloadEncrypted', $file->id) }}" method="GET" style="display:inline;">
                            <button type="submit" class="btn btn-info">Download Encrypted</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
=======
    <h2>Dencrypted Files</h2>
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
>>>>>>> a70eff78aa1e605e2b9c824e977ffe9c6b542b83
