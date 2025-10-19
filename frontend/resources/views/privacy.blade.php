@extends('layouts.app')

@section('title', 'Privacy Policy | Book Heaven')

@section('content')
<main class="privacy-main">
    <div class="privacy-container">
        <h1>Book Heaven Privacy Policy</h1>
        <p class="last-updated">Last Updated: January 1, 2023</p>

        <p>
            Welcome to Book Heaven! We are committed to protecting your privacy and ensuring the security of your personal information.
            This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
        </p>

        <h2>1. Information We Collect</h2>
        <p>We may collect the following types of information:</p>
        <ul>
            <li><strong>Personal Information:</strong> Name, email address, phone number, shipping/billing address when you create an account or make a purchase.</li>
            <li><strong>Payment Information:</strong> Credit card details or other payment information (processed securely through our payment processors).</li>
            <li><strong>Usage Data:</strong> Information about how you interact with our website, including pages visited, time spent, and browsing behavior.</li>
            <li><strong>Cookies and Tracking Technologies:</strong> We use cookies to enhance your experience and analyze site usage.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <p>We use the information we collect for various purposes:</p>
        <ul>
            <li>To process and fulfill your book orders</li>
            <li>To personalize your experience and recommend books you might enjoy</li>
            <li>To improve our website and services</li>
            <li>To communicate with you about orders, promotions, and updates</li>
            <li>To prevent fraud and enhance security</li>
            <li>To comply with legal obligations</li>
        </ul>

        <h2>3. Sharing of Information</h2>
        <p>We do not sell your personal information. We may share information with:</p>
        <ul>
            <li>Service providers who assist with payment processing, shipping, and marketing</li>
            <li>Legal authorities when required by law</li>
            <li>Business partners in case of mergers or acquisitions</li>
        </ul>

        <h2>4. Data Security</h2>
        <p>We implement industry-standard security measures to protect your information, including:</p>
        <ul>
            <li>SSL encryption for all data transmissions</li>
            <li>Secure storage of sensitive information</li>
            <li>Regular security audits</li>
        </ul>

        <h2>5. Your Rights and Choices</h2>
        <p>You have certain rights regarding your personal information:</p>
        <ul>
            <li>Access and update your account information</li>
            <li>Opt-out of marketing communications</li>
            <li>Request deletion of your account (subject to legal requirements)</li>
            <li>Manage cookie preferences through your browser settings</li>
        </ul>

        <h2>6. Children's Privacy</h2>
        <p>
            Book Heaven does not knowingly collect information from children under 13.
            If we become aware of such collection, we will take steps to delete the information promptly.
        </p>

        <h2>7. Changes to This Policy</h2>
        <p>
            We may update this Privacy Policy periodically. We will notify you of significant changes through our website or email.
            Your continued use of our services constitutes acceptance of the updated policy.
        </p>

        <div class="contact-info">
            <h2>8. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
            <ul>
                <li>Email: privacy@bookheaven.com</li>
                <li>Phone: (555) 123-4567</li>
                <li>Mail: Book Heaven Privacy Office, 123 Literary Lane, Bookville, BV 98765</li>
            </ul>
        </div>
    </div>
</main>

<style>
:root {
    --primary-color: #57abd2;
    --secondary-color: #f8f5fc;
    --accent-color: rgb(223, 219, 227);
    --text-color: #333;
    --light-purple: #e6d9f2;
    --card-bg: #f8f9fa;
    --aside-bg: #f0f2f5;
}

body {
    background-color: var(--secondary-color);
    color: var(--text-color);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.privacy-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.privacy-container {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

h1 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.2rem;
}

h2 {
    color: var(--primary-color);
    margin-top: 2rem;
    border-bottom: 2px solid var(--accent-color);
    padding-bottom: 0.5rem;
}

.last-updated {
    text-align: right;
    font-style: italic;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

ul {
    margin-left: 1.5rem;
    line-height: 1.7;
}

.contact-info {
    background-color: var(--light-purple);
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .privacy-main {
        padding: 1rem;
    }

    h1 {
        font-size: 1.8rem;
    }
}
</style>
@endsection
