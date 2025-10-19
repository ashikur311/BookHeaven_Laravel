@extends('layouts.app')

@section('title', 'FAQs | Book Heaven')

@section('content')
<main class="faq-main">
    <h1>Frequently Asked Questions</h1>

    <div class="faq-categories">
        <button class="category-btn active" data-category="all">All Questions</button>
        <button class="category-btn" data-category="orders">Orders</button>
        <button class="category-btn" data-category="shipping">Shipping</button>
        <button class="category-btn" data-category="returns">Returns</button>
        <button class="category-btn" data-category="account">Account</button>
    </div>

    <div class="faq-section">
        <h2>Order Questions</h2>

        <div class="faq-item" data-category="orders">
            <div class="faq-question">
                <span>How do I place an order?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>To place an order, simply browse our collection, add items to your cart, and proceed to checkout. You'll need to provide shipping information and payment details to complete your purchase.</p>
            </div>
        </div>

        <div class="faq-item" data-category="orders">
            <div class="faq-question">
                <span>Can I modify or cancel my order?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>You can modify or cancel your order within 1 hour of placing it by contacting our customer service team. After this window, your order will likely have entered our processing system and cannot be changed.</p>
            </div>
        </div>

        <div class="faq-item" data-category="orders">
            <div class="faq-question">
                <span>How do I track my order?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>Once your order ships, you'll receive a confirmation email with tracking information. You can also track your order by logging into your account and viewing your order history.</p>
            </div>
        </div>
    </div>

    <div class="faq-section">
        <h2>Shipping Information</h2>

        <div class="faq-item" data-category="shipping">
            <div class="faq-question">
                <span>What shipping options are available?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>We offer standard (3–5 business days), expedited (2 business days), and express (next business day) shipping options. International shipping is also available with varying delivery times.</p>
            </div>
        </div>

        <div class="faq-item" data-category="shipping">
            <div class="faq-question">
                <span>Do you ship internationally?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>Yes, we ship to most countries worldwide. International shipping rates and delivery times vary by destination. Please check our shipping calculator at checkout for details.</p>
            </div>
        </div>

        <div class="faq-item" data-category="shipping">
            <div class="faq-question">
                <span>How much does shipping cost?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>We offer free standard shipping on all orders over $35. For orders below this amount, standard shipping is $4.99. Expedited and express shipping options are available at additional costs.</p>
            </div>
        </div>
    </div>

    <div class="faq-section">
        <h2>Returns & Exchanges</h2>

        <div class="faq-item" data-category="returns">
            <div class="faq-question">
                <span>What is your return policy?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>We accept returns within 30 days of delivery for a full refund. Items must be in new, unread condition with original packaging. Contact us to initiate a return.</p>
            </div>
        </div>

        <div class="faq-item" data-category="returns">
            <div class="faq-question">
                <span>How do I return an item?</span>
                <span class="toggle-icon">+</span>
            </div>
            <div class="faq-answer">
                <p>To return an item, log into your account and visit the "My Orders" section to initiate a return. You'll receive a return label and instructions. For guest purchases, contact our customer service team.</p>
            </div>
        </div>
    </div>

    <div class="still-questions">
        <h2>Still have questions?</h2>
        <p>If you can't find the answer you're looking for, our customer service team is happy to help.</p>
        <a href="{{ url('/contact') }}">
            <button>Contact Us</button>
        </a>
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

.faq-main {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

h1 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 2rem;
}

.faq-categories {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.category-btn {
    background-color: var(--card-bg);
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.category-btn:hover, .category-btn.active {
    background-color: var(--primary-color);
    color: white;
}

.faq-section {
    margin-bottom: 3rem;
}

.faq-section h2 {
    color: var(--primary-color);
    border-bottom: 2px solid var(--accent-color);
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.faq-item {
    background-color: var(--card-bg);
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    overflow: hidden;
}

.faq-question {
    padding: 1.5rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: var(--aside-bg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-item.active .faq-answer {
    padding: 0 1.5rem 1.5rem;
    max-height: 500px;
}

.toggle-icon {
    font-size: 1.2rem;
    transition: transform 0.3s;
}

.faq-item.active .toggle-icon {
    transform: rotate(45deg);
}

.still-questions {
    text-align: center;
    margin-top: 3rem;
    padding: 2rem;
    background-color: var(--light-purple);
    border-radius: 10px;
}

.still-questions button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 1rem;
    transition: background 0.3s;
}

.still-questions button:hover {
    background-color: #3a8db3;
}

@media (max-width: 768px) {
    .faq-main {
        padding: 1rem;
    }

    .faq-question {
        padding: 1rem;
    }
}
</style>

<script>
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        question.parentElement.classList.toggle('active');
    });
});

document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const category = btn.dataset.category;
        document.querySelectorAll('.faq-item').forEach(item => {
            item.style.display = (category === 'all' || item.dataset.category === category)
                ? 'block' : 'none';
        });
    });
});
</script>
@endsection
