<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lumina University - Department of Digital Learning & Research recruitment portal.">
    <title>Home | Lumina University Digital Learning</title>
    
    <link rel="stylesheet" href="styles/styles.css">
    
    <style>
        <!-- inline CSS for highlighting key -->
        .highlight-text {
            background-color: #b58e42; 
            color: #000000; 
            padding: 2px 5px;
            font-weight: bold;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <!-- Shared site header -->
    <?php require_once("header.inc"); ?>

    <!-- Shared site navigation menu -->
    <?php require_once("nav.inc"); ?>

    <main>

        <!-- Hero section with welcome message and department overview -->
        <section id="hero-section" class="hero">
            <div class="hero-content">
                <h3>Join the Forefront of Digital Education</h3>
                <p>Welcome to the Lumina University Department of Digital Learning & Research. We are dedicated to transforming the educational landscape by integrating cutting-edge technology with evidence-based pedagogy.</p>
            </div>
        </section>

        <!-- Section highlighting the department's mission and vision -->
        <section class="content-section">
            <h3>Who We Are</h3>
            <p>Our department supports academic staff and students by developing engaging digital learning environments, conducting pioneering research in educational technology, and fostering a culture of continuous digital capacity building.</p>
            <p>We are currently seeking passionate professionals to join our team. If you have a <span class="highlight-text">drive for educational technology</span> and want to make a lasting impact on university education, we want to hear from you!</p>
            
            <!-- Call-to-action button linking to the application form -->
 <figure class="main-image-container">
    <img src="images/studentvr.png" 
         alt="Student using a VR headset in an educational setting" 
         class="main-image">
    <figcaption>Students trial VR-based learning tools in the Digital Learning Lab.</figcaption>
</figure> 
        </section>

        <hr class="section-divider">

        <!-- Core focus areas: three-card grid summarising the department's main research/service pillars -->
        <section class="content-section">
            <h3>Our Core Focus Areas</h3>
            <div class="focus-grid">
                <article class="focus-card">
                    <h4>Immersive Learning</h4>
                    <p>Researching the impact of Virtual Reality (VR) and Augmented Reality (AR) in tertiary science and medical education.</p>
                </article>
                <article class="focus-card">
                    <h4>Learning Analytics</h4>
                    <p>Utilizing big data to predict student success rates and tailor personalized intervention strategies.</p>
                </article>
                <article class="focus-card">
                    <h4>Accessible Tech</h4>
                    <p>Developing digital tools that ensure education is fully accessible to students of all physical and cognitive abilities.</p>
                </article>
            </div>
        </section>

        <hr class="section-divider">

        <!-- Recruitment benefits + staff testimonial, side-by-side layout -->
        <section class="content-section split-section">
            <div class="benefits">
                <h3>Why Join Lumina?</h3>
                <ul class="benefits-list">
                    <li>Generous university superannuation contributions (17%).</li>
                    <li>Access to state-of-the-art research laboratories.</li>
                    <li>Flexible, hybrid working arrangements.</li>
                    <li>Subsidized postgraduate study opportunities.</li>
                    <li>On-campus childcare and wellness facilities.</li>
                </ul>
            </div>
            
            <div class="testimonial">
                <h3>Staff Spotlight</h3>
                <blockquote>
                    <p>"Working at Lumina has allowed me to push the boundaries of what is possible in digital education. The supportive environment and focus on real-world impact make it an incredible place to build a career."</p>
                    &mdash; Dr. Sarah Jenkins, Lead Learning Designer
                </blockquote>
            </div>
        </section>

    </main>

    
    <?php require_once("footer.inc"); ?>

</body>
</html>