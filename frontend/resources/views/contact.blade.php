@extends('layouts.app')

@section('title', 'Contact Us | Book Heaven')

@section('content')
<main class="contact-main">
    <h1>Contact Book Heaven</h1>

    <div class="contact-container">
        <div class="contact-card">
            <div class="contact-icon">📞</div>
            <h2>Phone Support</h2>
            <p>Our customer service team is available:</p>
            <p><strong>Monday-Friday:</strong> 9am-6pm EST</p>
            <p><strong>Saturday:</strong> 10am-4pm EST</p>
            <p><strong>Phone:</strong> (555) 123-4567</p>
        </div>

        <div class="contact-card">
            <div class="contact-icon">✉️</div>
            <h2>Email Us</h2>
            <p>Send us an email and we'll respond within 24 hours:</p>
            <p><strong>Customer Service:</strong> help@bookheaven.com</p>
            <p><strong>Business Inquiries:</strong> business@bookheaven.com</p>
            <p><strong>Press:</strong> press@bookheaven.com</p>
        </div>

        <div class="contact-card">
            <div class="contact-icon">🏠</div>
            <h2>Visit Us</h2>
            <p>Our flagship store location:</p>
            <p><strong>Address:</strong><br>
            123 Literary Lane<br>
            Bookville, BV 98765</p>
            <p><strong>Hours:</strong> 10am-8pm Daily</p>
        </div>
    </div>

    <div class="contact-form">
        <h2>Send Us a Message</h2>
        <form method="POST" action="#">
            @csrf
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject">
                    <option value="general">General Inquiry</option>
                    <option value="order">Order Question</option>
                    <option value="returns">Returns/Exchanges</option>
                    <option value="business">Business Inquiry</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>

            <button type="submit">Send Message</button>
        </form>
    </div>
</main>

<style>
:root {
    --primary-color: #57abd2;
    --secondary-color: #f8f5fc;
    --accent-color: rgb(223, 219, 227);
    --text-color: #333;
    --light-purple: #e6d9f2;
    --dark-text: #212529;
    --light-text: #f8f9fa;
    --card-bg: #f8f9fa;
    --aside-bg: #f0f2f5;
    --nav-hover: #e0e0e0;
}

.dark-mode {
    --primary-color: #57abd2;
    --secondary-color: #2d3748;
    --accent-color: #4a5568;
    --text-color: #f8f9fa;
    --light-purple: #4a5568;
    --dark-text: #f8f9fa;
    --light-text: #212529;
    --card-bg: #1a202c;
    --aside-bg: #1a202c;
    --nav-hover: #4a5568;
}

.contact-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

h1 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 2rem;
}

.contact-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.contact-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    text-align: center;
}
.contact-card:hover {
    transform: translateY(-5px);
}

.contact-card h2 {
    color: var(--primary-color);
    margin-top: 0;
}

.contact-icon {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.contact-form {
    background-color: var(--card-bg);
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto 3rem;
}

.contact-form h2 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

input, textarea, select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--accent-color);
    border-radius: 5px;
    background-color: var(--secondary-color);
    color: var(--text-color);
}

button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #3a8db3;
}

@media (max-width: 768px) {
    .contact-main {
        padding: 1rem;
    }
    .contact-container {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
