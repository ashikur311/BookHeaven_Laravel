@extends('layouts.app')

@section('title', 'Shipping Policy | Book Heaven')

@section('content')
<main class="shipping-main">
    <h1>Shipping Policy</h1>

    <section class="intro">
        <p>
            At Book Heaven, we strive to deliver your books quickly and safely.
            Please review our shipping policy below for information about methods, delivery times, and fees.
        </p>
    </section>

    <section class="methods">
        <h2>Shipping Methods & Delivery Times</h2>
        <div class="shipping-cards">
            <div class="card">
                <h3>Standard Shipping</h3>
                <p><strong>Delivery Time:</strong> 3–5 business days</p>
                <p><strong>Cost:</strong> Free on orders over $35, otherwise $4.99</p>
                <p>Our most economical option with reliable delivery times.</p>
            </div>

            <div class="card">
                <h3>Expedited Shipping</h3>
                <p><strong>Delivery Time:</strong> 2 business days</p>
                <p><strong>Cost:</strong> $9.99</p>
                <p>Priority processing and faster delivery for urgent orders.</p>
            </div>

            <div class="card">
                <h3>Express Shipping</h3>
                <p><strong>Delivery Time:</strong> 1 business day</p>
                <p><strong>Cost:</strong> $14.99</p>
                <p>When you need your books as soon as possible.</p>
            </div>
        </div>
    </section>

    <section class="delivery-times">
        <h2>Estimated Delivery Times by Region</h2>
        <table class="delivery-table">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Standard</th>
                    <th>Expedited</th>
                    <th>Express</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>East Coast</td>
                    <td>2–3 days</td>
                    <td>1–2 days</td>
                    <td>Next day</td>
                </tr>
                <tr>
                    <td>Midwest</td>
                    <td>3–4 days</td>
                    <td>2 days</td>
                    <td>Next day</td>
                </tr>
                <tr>
                    <td>West Coast</td>
                    <td>4–5 days</td>
                    <td>2 days</td>
                    <td>Next day</td>
                </tr>
                <tr>
                    <td>International</td>
                    <td>7–14 days</td>
                    <td>5–7 days</td>
                    <td>3–5 days</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="processing">
        <h2>Order Processing Time</h2>
        <p>
            All orders are processed within 1–2 business days (excluding weekends and holidays)
            after receiving your confirmation email. You’ll receive another notification when your order ships.
        </p>
    </section>

    <section class="important-notes">
        <h2>Important Notes</h2>
        <ul>
            <li>Delivery times are estimates and not guaranteed.</li>
            <li>Customs and import taxes may apply to international orders.</li>
            <li>Remote areas may experience longer delivery times.</li>
            <li>Delivery times may be extended during peak seasons.</li>
        </ul>
    </section>

    <section class="international">
        <h2>International Shipping</h2>
        <p>
            We ship to most countries worldwide. International orders may incur customs fees or import duties,
            which are the customer’s responsibility. Book Heaven cannot predict or control these charges.
        </p>
    </section>

    <section class="tracking">
        <h2>Tracking Your Order</h2>
        <p>
            Once shipped, you’ll receive an email with tracking information.
            You can track your package through the provided link or by logging into your account.
        </p>
    </section>

    <section class="restrictions">
        <h2>Shipping Restrictions</h2>
        <p>
            Some items may be restricted for shipping to certain locations. If we can’t ship an item to your area,
            we’ll notify you during checkout or shortly after placing your order.
        </p>
    </section>

    <section class="questions">
        <h2>Questions?</h2>
        <p>
            If you have any questions about our shipping policy, contact us at
            <a href="mailto:shipping@bookheaven.com">shipping@bookheaven.com</a>
            or call (555) 123-4567.
        </p>
    </section>
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

.shipping-main {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

h1 {
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 2rem;
}

h2 {
    color: var(--primary-color);
    border-bottom: 2px solid var(--accent-color);
    padding-bottom: 0.5rem;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.shipping-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.card {
    background-color: var(--aside-bg);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.delivery-table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
}

.delivery-table th, .delivery-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--accent-color);
}

.delivery-table th {
    background-color: var(--light-purple);
}

.important-notes ul {
    background-color: var(--light-purple);
    padding: 1.5rem;
    border-radius: 8px;
    list-style-type: disc;
    margin-left: 2rem;
}

.questions {
    text-align: center;
    margin-top: 3rem;
    background-color: var(--light-purple);
    padding: 2rem;
    border-radius: 10px;
}
</style>
@endsection
