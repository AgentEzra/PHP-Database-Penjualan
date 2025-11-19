<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Cafe Ngoding</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="index.php">Orders</a>

            <?php if (isUser()): ?>
            <a href="contact.php">Contact</a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <a href="users.php">Manage Users</a>
                <a href="create.php">Create Order</a>
            <?php endif; ?>
        </div>

        <div class="contact-container">
            <div class="contact-header">
                <h1>Get In Touch</h1>
                <p>We'd love to hear from you! Whether you have questions, feedback, or just want to say hello, feel free to reach out to us.</p>
            </div>

            <?php
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = mysqli_real_escape_string($connect, $_POST['name']);
                $email = mysqli_real_escape_string($connect, $_POST['email']);
                $subject = mysqli_real_escape_string($connect, $_POST['subject']);
                $message = mysqli_real_escape_string($connect, $_POST['message']);
                
                // In a real application, you would:
                // 1. Send an email
                // 2. Save to database
                // 3. Process the message
                
                $success = true; // Simulate successful submission
            }
            ?>

            <?php if (isset($success) && $success): ?>
            <div class="success-message" style="display: block;">
                Thank you for your message! We'll get back to you soon.
            </div>
            <?php endif; ?>

            <div class="contact-content">
                <div class="contact-info">
                    <h2>Contact Information</h2>
                    
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-content">
                            <h3>Our Location</h3>
                            <p>SMKN 02 Jakarta<br>Jl. batu, Gambir<br>Indonesia</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-content">
                            <h3>Phone Number</h3>
                            <p>+62 858-8559-12XX<br>+62 857-7351-97XX</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div class="info-content">
                            <h3>Email Address</h3>
                            <p>sinisterezra@cafengoding.com<br>support@cafengoding.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">💬</div>
                        <div class="info-content">
                            <h3>Social Media</h3>
                            <p>Instagram: @cafengoding<br>Twitter: @cafengoding</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="feedback">Feedback</option>
                                <option value="complaint">Complaint</option>
                                <option value="partnership">Partnership</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required placeholder="Tell us how we can help you..."></textarea>
                        </div>

                        <button type="submit" class="btn-full">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="store-hours">
                <h2>Store Operational Hour</h2>
                <div class="hours-grid">
                    <?php
                    $hours = [
                        'Monday' => '6:30 AM - 15:00 PM',
                        'Tuesday' => '6:30 AM - 15:00 PM',
                        'Wednesday' => '6:30 AM - 15:00 PM',
                        'Thursday' => '6:30 AM - 15:00 PM',
                        'Friday' => '6:30 AM - 15:00 PM',
                        'Saturday' => 'Closed',
                        'Sunday' => 'Closed'
                    ];
                    
                    $today = date('l');
                    ?>
                    
                    <?php foreach ($hours as $day => $time): ?>
                    <div class="hour-item <?= $day === $today ? 'current-day' : '' ?>">
                        <span class="day"><?= $day ?></span>
                        <span class="time"><?= $time ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="map-container">
                <h2>Find Us</h2>
                <div class="map-placeholder">
                    <iframe class="map-placeholder" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15866.582775169423!2d106.81590743830506!3d-6.1781595827596965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4330fc1f097%3A0x380210b0f3ed4996!2sVocational%20High%20School%20State%202%20Of%20central%20Jakarta!5e0!3m2!1sen!2sid!4v1762871128197!5m2!1sen!2sid" 
                        width="1000" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div style="text-align: center; margin-top: 1rem;">
                    <p><strong>Directions :</strong> We're located in Gambir, right beside Kementrian Kelautan dan Perikanan. 
                    Look for our school building right next to it.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.querySelector('form');
            const successMessage = document.querySelector('.success-message');
            
            contactForm.addEventListener('submit', function(e) {
                let isValid = true;
                const inputs = contactForm.querySelectorAll('input[required], textarea[required], select[required]');
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.style.borderColor = '#dc3545';
                    } else {
                        input.style.borderColor = '#e1e1e1';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
            
            // Auto-fill email if available in session (you might want to store email in session)
            const emailInput = document.getElementById('email');
            if (!emailInput.value) {
                emailInput.placeholder = 'your.email@example.com';
            }
        });
    </script>
</body>
</html>