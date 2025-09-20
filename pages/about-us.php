<?php
$page_title = 'About The Mobile Store';
require_once '../includes/header.php';
?>

<style>
    .about-header {
        text-align: center;
        padding: 150px 20px 50px;
        background: var(--glass);
    }

    .about-section {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .about-section h2 {
        font-size: 2.2rem;
        margin-bottom: 20px;
        text-align: center;
        background: var(--text-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .team-member-card {
        background: var(--body);
        border-radius: 15px;
        box-shadow: var(--shadow);
        text-align: center;
        padding: 25px;
        transition: all 0.3s ease;
    }

    .team-member-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--dark-shadow);
    }

    .team-member-card img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid var(--blue);
    }

    .team-member-card h4 {
        font-size: 1.4rem;
        margin-bottom: 5px;
    }

    .team-member-card p {
        color: var(--dark);
        font-style: italic;
    }
</style>

<main>
    <div class="about-header">
        <div class="section-title">
            <h2>Our Story</h2>
            <div class="title-line"></div>
        </div>
        <p style="max-width: 800px; margin: 0 auto; font-size: 1.2rem;">Connecting Jamnagar to the world, one phone at a time.</p>
    </div>

    <section class="about-section">
        <h2>Who We Are</h2>
        <p>
            Founded in 2024, The Mobile Store was born from a passion for technology and a desire to bring the latest mobile innovations to our community in Jamnagar, Gujarat. We noticed a gap in the market for a trusted, local retailer that not only offered a wide selection of top-tier smartphones but also provided expert advice and dedicated customer service. We decided to be that change.
        </p>
        <p>
            From our humble beginnings, we've grown into a premier destination for mobile technology enthusiasts and everyday users alike. Our core mission is simple: to empower our customers by providing them with the tools and technology they need to thrive in a digital world.
        </p>
    </section>

    <section class="about-section" style="background: var(--glass); padding: 40px; border-radius: 15px;">
        <h2>Our Values</h2>
        <div class="team-grid">
            <div class="team-member-card">
                <h4>Customer First</h4>
                <p>Your needs are our priority. We are committed to providing personalized service and ensuring you find the perfect device.</p>
            </div>
            <div class="team-member-card">
                <h4>Quality & Authenticity</h4>
                <p>We only source from trusted suppliers, guaranteeing 100% genuine products with official warranties.</p>
            </div>
            <div class="team-member-card">
                <h4>Community Focused</h4>
                <p>As a local business from Jamnagar, we are dedicated to serving and growing with our community.</p>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2>Meet the Team</h2>
        <div class="team-grid">
            <div class="team-member-card">
                <img src="https://placehold.co/120x120/004EE0/FFFFFF?text=AV" alt="Team Member">
                <h4>Akash Varma</h4>
                <p>Founder & CEO</p>
            </div>
            <div class="team-member-card">
                <img src="https://placehold.co/120x120/1883FF/FFFFFF?text=PS" alt="Team Member">
                <h4>Priya Sharma</h4>
                <p>Head of Sales</p>
            </div>
            <div class="team-member-card">
                <img src="https://placehold.co/120x120/042E7B/FFFFFF?text=RJ" alt="Team Member">
                <h4>Rohan Joshi</h4>
                <p>Tech Support Lead</p>
            </div>
        </div>
    </section>

</main>

<?php
require_once '../includes/footer.php';
?>