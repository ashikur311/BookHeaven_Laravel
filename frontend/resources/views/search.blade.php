@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Search results for "{{ $query }}"</h2>

    @if($books->count())
        <div class="book-grid">
            @foreach($books as $book)
                <div class="book-item">
                    <h4>{{ $book->title }}</h4>
                    <p>{{ Str::limit($book->description, 100) }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p>No results found.</p>
    @endif
</div>
@endsection
