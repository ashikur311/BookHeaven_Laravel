@extends('layouts.app')
@section('title', 'Become a Partner')

@section('content')
<style>
    body {
        background-color: #f8f5fc;
    }

    .partner-center-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 85vh;
        padding: 20px;
    }

    .partner-agreement-container {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 40px 50px;
        max-width: 700px;
        width: 100%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .partner-agreement-title {
        color: #57abd2;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .agreement-content {
        color: #333;
        line-height: 1.7;
        font-size: 1rem;
    }

    .agreement-content h3 {
        color: #57abd2;
        margin-top: 25px;
        margin-bottom: 15px;
        font-size: 1.2rem;
        text-align: left;
    }

    .agreement-points {
        text-align: left;
        margin-bottom: 25px;
    }

    .agreement-points li {
        margin-bottom: 10px;
    }

    .btn-primary {
        background-color: #57abd2;
        border: none;
        color: #fff;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #3a95bb;
    }

    @media (max-width: 768px) {
        .partner-agreement-container {
            padding: 25px 20px;
        }
        .partner-agreement-title {
            font-size: 1.6rem;
        }
    }
</style>

<div class="partner-center-wrapper">
    <div class="partner-agreement-container">
        <h1 class="partner-agreement-title">Become a Partner</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="agreement-content">
            <p>
                You are not currently registered as a partner.  
                By becoming a partner, you can earn money by sharing your books with our community.
            </p>

            <h3>Partner Agreement:</h3>
            <ul class="agreement-points">
                <li>You retain ownership of all books you share</li>
                <li>You earn 70% of the rental revenue for each book</li>
                <li>Books must be legally owned and in good condition</li>
                <li>You are responsible for shipping and timely returns</li>
            </ul>

            <p>By clicking below, you agree to these terms.</p>
        </div>

        <form method="POST" action="{{ route('partner.become') }}">
            @csrf
            <button type="submit" class="btn-primary">Become Partner</button>
        </form>
    </div>
</div>
@endsection
