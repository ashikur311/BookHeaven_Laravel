<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Book Haven</h3>
            <p>Your literary paradise since 2010. We're dedicated to bringing you the best books from around the world.</p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Goodreads"><i class="fab fa-goodreads"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ url('writer_books') }}">Authors</a></li>
                <li><a href="{{ url('genre_books') }}">Genres</a></li>
                <li><a href="{{ url('about') }}">About Us</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Customer Service</h3>
            <ul>
                <li><a href="{{ url('contact') }}">Contact Us</a></li>
                <li><a href="{{ url('faq') }}">FAQs</a></li>
                <li><a href="{{ url('shipping') }}">Shipping Policy</a></li>
                <li><a href="{{ url('privacy') }}">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Contact Info</h3>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> 123 Book Street, Dhaka</li>
                <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                <li><i class="fas fa-envelope"></i> info@bookhaven.com</li>
                <li><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</li>
            </ul>
        </div>
    </div>

    <div class="copyright">
        <p>&copy; {{ date('Y') }} Book Haven. All rights reserved.</p>
    </div>
</footer>

<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
