@extends('layouts.app')
@section('title', 'Add Book for Rent')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/partner_add_books.css') }}">

<div class="main-container">
    {{-- Sidebar --}}
    <aside>
        <div class="nav-logo">
            {{ $username }}
            <div style="font-size: 0.8rem; margin-top: 5px;">
                Partner since {{ \Carbon\Carbon::parse($partner->joined_at)->format('M Y') }}
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="{{ route('partner.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('partner.addBook') }}" class="active"><i class="fas fa-book"></i> Add Book</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Form --}}
    <main>
        <div class="page-header">
            <h1 class="page-title">Add Book for Rent</h1>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="status-message success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="status-message error">{{ session('error') }}</div>
        @endif

        <div class="card">
            <h2 class="card-header">Book Information</h2>
            <form id="bookForm" method="POST" enctype="multipart/form-data" action="{{ route('partner.storeBook') }}">
                @csrf
                <div class="form-group">
                    <label for="bookTitle">Book Title *</label>
                    <input type="text" id="bookTitle" name="bookTitle" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="bookWriter">Writer *</label>
                    <input type="text" id="bookWriter" name="bookWriter" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="bookGenre">Genre *</label>
                    <input type="text" id="bookGenre" name="bookGenre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="bookLanguage">Language</label>
                    <input type="text" id="bookLanguage" name="bookLanguage" class="form-control" value="English">
                </div>

                <div class="form-group">
                    <label for="bookDescription">Description *</label>
                    <textarea id="bookDescription" name="bookDescription" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label>Book Cover *</label>
                    <label for="bookCover" class="file-input-label"><i class="fas fa-upload"></i> Choose Cover Image</label>
                    <input type="file" id="bookCover" name="bookCover" accept="image/*" required>
                    <img id="coverPreview" class="preview-image" src="#" alt="Cover Preview">
                    <p class="file-requirements">(JPEG, PNG, or GIF, max 5MB)</p>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Submit Book</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coverInput = document.getElementById('bookCover');
    const preview = document.getElementById('coverPreview');

    coverInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = event => {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('bookForm').reset();
        preview.style.display = 'none';
    });
});
</script>
@endsection
