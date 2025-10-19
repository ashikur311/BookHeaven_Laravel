@extends('layouts.app')

@section('title', 'About Us | Book Heaven')

@section('content')
<main>
    <div class="about-hero">
        <img src="https://via.placeholder.com/200" alt="Book Heaven Logo">
        <h1>Welcome to Book Heaven</h1>
        <p style="max-width: 800px; font-size: 1.2rem;">
            Your ultimate destination for discovering, exploring, and collecting books from around the world. 
            Since 2010, we've been connecting readers with their next favorite book.
        </p>
    </div>
    
    <div class="about-section">
        <h2>Our Story</h2>
        <p>Book Heaven began as a small independent bookstore in a quiet neighborhood. What started as a passion project between two literature-loving friends quickly grew into one of the most beloved book destinations in the country.</p>
        <p>In 2015, we launched our online store to share our carefully curated collection with book lovers worldwide. Today, we serve thousands of customers across the globe while maintaining the personal touch and expert recommendations that made our physical store so special.</p>
    </div>
    
    <div class="about-section">
        <h2>Our Mission</h2>
        <p>At Book Heaven, we believe in the transformative power of books. Our mission is to:</p>
        <ul>
            <li>Connect readers with books that inspire, educate, and entertain</li>
            <li>Support authors and publishers by promoting diverse voices</li>
            <li>Foster a community of book lovers through events and discussions</li>
            <li>Make quality literature accessible to everyone</li>
        </ul>
    </div>
    
    <div class="about-section">
        <h2>Our Values</h2>
        <div class="values-list">
            <div class="value-item">
                <div class="value-icon">📚</div>
                <h3>Literary Excellence</h3>
                <p>We carefully select each title in our collection for its quality and impact.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">🤝</div>
                <h3>Community</h3>
                <p>We believe books bring people together and strengthen communities.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">🌍</div>
                <h3>Diversity</h3>
                <p>We champion diverse voices and perspectives in literature.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">💡</div>
                <h3>Knowledge</h3>
                <p>We're committed to spreading knowledge and fostering lifelong learning.</p>
            </div>
        </div>
    </div>
    
    <div class="about-section">
        <h2>Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Sarah Johnson">
                <h3>Sarah Johnson</h3>
                <p><em>Founder & CEO</em></p>
                <p>Literature professor turned entrepreneur, Sarah curates our fiction collection.</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Michael Chen">
                <h3>Michael Chen</h3>
                <p><em>Co-Founder & COO</em></p>
                <p>Former librarian with an encyclopedic knowledge of non-fiction titles.</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Emma Rodriguez">
                <h3>Emma Rodriguez</h3>
                <p><em>Head of Customer Experience</em></p>
                <p>Makes sure every Book Heaven customer feels valued and understood.</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="David Kim">
                <h3>David Kim</h3>
                <p><em>Head Buyer</em></p>
                <p>Our resident expert on emerging authors and independent presses.</p>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <h2>Join Our Community</h2>
        <p style="max-width: 700px; margin: 0 auto 1.5rem;">
            Sign up for our newsletter to receive book recommendations, author interviews, and exclusive offers straight to your inbox.
        </p>
        <form style="display: flex; max-width: 500px; margin: 0 auto;">
            <input type="email" placeholder="Your email address" 
                   style="flex-grow: 1; padding: 0.75rem; border: 1px solid var(--accent-color); 
                          border-radius: 5px 0 0 5px; border-right: none;">
            <button type="submit" 
                    style="background-color: var(--primary-color); color: white; border: none; 
                           padding: 0 1.5rem; border-radius: 0 5px 5px 0; cursor: pointer;">Subscribe</button>
        </form>
    </div>
</main>

<link rel="stylesheet" href="{{ asset('css/about.css') }}">


@endsection

