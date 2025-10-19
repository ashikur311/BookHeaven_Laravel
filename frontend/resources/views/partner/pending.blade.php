@extends('layouts.app')
@section('title', 'Partner Approval Pending')

@section('content')
<style>
    body {
        background-color: #f8f5fc;
    }

    .pending-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 85vh;
        padding: 20px;
    }

    .pending-container {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 50px 60px;
        max-width: 650px;
        width: 100%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .pending-icon {
        font-size: 3rem;
        color: #ffc107;
        margin-bottom: 20px;
    }

    .pending-title {
        color: #57abd2;
        font-size: 1.9rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .pending-message {
        color: #333;
        line-height: 1.7;
        font-size: 1rem;
        margin-bottom: 25px;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        .pending-container {
            padding: 35px 25px;
        }
        .pending-title {
            font-size: 1.6rem;
        }
    }
</style>

<div class="pending-wrapper">
    <div class="pending-container">
        <div class="pending-icon"><i class="fas fa-hourglass-half"></i></div>
        <h1 class="pending-title">Partner Approval Pending</h1>
        <p class="pending-message">
            Thank you for applying, <strong>{{ $partner->username }}</strong>.<br>
            Your application is currently under review by our admin team.
        </p>
        <div class="alert alert-info">
            You’ll be notified via email once your application is approved.
        </div>
    </div>
</div>
@endsection
