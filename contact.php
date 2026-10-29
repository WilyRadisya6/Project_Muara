<?php 
    $page_title = 'Contact';
    include 'includes/header.php'; 
?>

<section class="contact-section">
    <div class="contact-container">
        <div class="contact-header">
            <h1>Get in Touch</h1>
            <p>Have a question or suggestion? We'd love to hear from you!</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-form-wrapper">
                <h2>Send us a Message</h2>
                <form class="contact-form" method="POST" action="#">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-email">Email Address</label>
                        <input type="email" id="contact-email" name="email" placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" placeholder="Tell us more about your inquiry..." required></textarea>
                    </div>
                    
                    <button type="submit" class="primary-btn" style="width: 100%; padding: 16px;">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <h2>Contact Information</h2>
                
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div>
                        <h3>Address</h3>
                        <p>Jl. Merdeka No. 123<br>Jakarta, Indonesia 12345</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div>
                        <h3>Phone</h3>
                        <p><a href="tel:+6281234567890">+62 812 3456 7890</a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:info@yummyyamy.com">info@yummyyamy.com</a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">🕐</div>
                    <div>
                        <h3>Hours</h3>
                        <p>Monday - Friday: 10:00 - 22:00<br>Saturday - Sunday: 11:00 - 23:00</p>
                    </div>
                </div>
                
                <div class="social-info">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon">f</a>
                        <a href="#" class="social-icon">📷</a>
                        <a href="#" class="social-icon">🐦</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    include 'includes/footer.php';
?>
