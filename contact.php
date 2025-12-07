<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$message = '';
$error = '';
$success = false;

// Process contact form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors['subject'] = 'Subject is required';
    }
    
    if (empty($message_text)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message_text) < 10) {
        $errors['message'] = 'Message must be at least 10 characters';
    }
    
    if (empty($errors)) {
        try {
            $conn = $db->getConnection();
            $stmt = $conn->prepare("
                INSERT INTO contacts (name, email, phone, subject, message, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $stmt->execute([
                $name, $email, $phone, $subject, $message_text, $ip_address, $user_agent
            ]);
            
            $success = true;
            $message = 'Thank you for contacting us! We will get back to you soon.';
            
            // Clear form
            $name = $email = $phone = $subject = $message_text = '';
            
            // Send email notification (in production)
            // $to = "contact@justiceflow.com";
            // $email_subject = "New Contact Form Submission: $subject";
            // $email_body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message_text";
            // mail($to, $email_subject, $email_body);
            
        } catch (Exception $e) {
            $error = 'Sorry, there was an error sending your message. Please try again.';
        }
    } else {
        $error = 'Please correct the errors below.';
    }
}

// Get contact info from settings

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - JusticeFlow</title>
    <?php include 'includes/head.php'; ?>
    <style>
        .contact-form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .contact-info-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            height: 100%;
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(45, 116, 218, 0.25);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 116, 218, 0.3);
        }
        
        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        .office-hours {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .hours-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .hours-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            justify-content: space-between;
        }
        
        .hours-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); padding: 150px 0 100px; margin-top: 70px; color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Get in Touch</h1>
                    <p class="lead mb-4">Have questions about our legal tech solutions? Our team is here to help you navigate the future of legal services.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#contact-form" class="btn btn-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>Send Message
                        </a>
                        <a href="tel:<?php echo $contact_settings['contact_phone'] ?? '+1 (555) 123-4567'; ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-phone me-2"></i>Call Now
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-headset fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-form-card" id="contact-form">
                        <h2 class="fw-bold mb-4">Send us a Message</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-medium">Full Name *</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                           required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-medium">Email Address *</label>
                                    <input type="email" 
                                           class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                           required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-medium">Phone Number</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label fw-medium">Subject *</label>
                                    <select class="form-select <?php echo isset($errors['subject']) ? 'is-invalid' : ''; ?>" 
                                            id="subject" 
                                            name="subject" 
                                            required>
                                        <option value="">Select a subject</option>
                                        <option value="General Inquiry" <?php echo (isset($subject) && $subject == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                        <option value="Service Information" <?php echo (isset($subject) && $subject == 'Service Information') ? 'selected' : ''; ?>>Service Information</option>
                                        <option value="Technical Support" <?php echo (isset($subject) && $subject == 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                                        <option value="Partnership" <?php echo (isset($subject) && $subject == 'Partnership') ? 'selected' : ''; ?>>Partnership</option>
                                        <option value="Feedback" <?php echo (isset($subject) && $subject == 'Feedback') ? 'selected' : ''; ?>>Feedback</option>
                                        <option value="Other" <?php echo (isset($subject) && $subject == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['subject'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['subject']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label fw-medium">Your Message *</label>
                                <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          required><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['message']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy" required>
                                    <label class="form-check-label" for="privacy">
                                        I agree to the <a href="privacy.php" class="text-decoration-none">Privacy Policy</a> and consent to being contacted.
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="col-lg-4">
                    <div class="contact-info-card">
                        <h3 class="h4 mb-4">Contact Information</h3>
                        
                        <div class="mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h4 class="h5 mb-2">Our Office</h4>
                            <p class="mb-0">
                                <?php echo htmlspecialchars($contact_settings['contact_address'] ?? '123 Legal Street, San Francisco, CA 94107'); ?>
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h4 class="h5 mb-2">Call Us</h4>
                            <p class="mb-2">
                                <a href="tel:<?php echo htmlspecialchars($contact_settings['contact_phone'] ?? '+1 (555) 123-4567'); ?>" 
                                   class="text-white text-decoration-none">
                                    <?php echo htmlspecialchars($contact_settings['contact_phone'] ?? '+1 (555) 123-4567'); ?>
                                </a>
                            </p>
                            <p class="mb-0 small">Mon-Fri: 9:00 AM - 6:00 PM</p>
                        </div>
                        
                        <div class="mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4 class="h5 mb-2">Email Us</h4>
                            <p class="mb-0">
                                <a href="mailto:<?php echo htmlspecialchars($contact_settings['contact_email'] ?? 'contact@justiceflow.com'); ?>" 
                                   class="text-white text-decoration-none">
                                    <?php echo htmlspecialchars($contact_settings['contact_email'] ?? 'contact@justiceflow.com'); ?>
                                </a>
                            </p>
                        </div>
                        
                        <div class="mt-5">
                            <h4 class="h5 mb-3">Follow Us</h4>
                            <div class="d-flex gap-3">
                                <?php if (!empty($contact_settings['facebook_url'])): ?>
                                <a href="<?php echo htmlspecialchars($contact_settings['facebook_url']); ?>" 
                                   class="text-white fs-5">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($contact_settings['twitter_url'])): ?>
                                <a href="<?php echo htmlspecialchars($contact_settings['twitter_url']); ?>" 
                                   class="text-white fs-5">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($contact_settings['linkedin_url'])): ?>
                                <a href="<?php echo htmlspecialchars($contact_settings['linkedin_url']); ?>" 
                                   class="text-white fs-5">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($contact_settings['instagram_url'])): ?>
                                <a href="<?php echo htmlspecialchars($contact_settings['instagram_url']); ?>" 
                                   class="text-white fs-5">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Map & Office Hours -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-5">
                <!-- Map -->
                <div class="col-lg-8">
                    <div class="map-container">
                        <!-- Google Map Embed -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.681434336378!2d-122.41941608468188!3d37.77492977975923!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808c9e3b3b3f%3A0x1c5b5d3a3b3b3b3b!2sSan%20Francisco%2C%20CA%2C%20USA!5e0!3m2!1sen!2s!4v1623456789012!5m2!1sen!2s" 
                                width="100%" 
                                height="400" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                        </iframe>
                    </div>
                </div>
                
                <!-- Office Hours -->
                <div class="col-lg-4">
                    <div class="office-hours">
                        <h3 class="h4 mb-4">Office Hours</h3>
                        <ul class="hours-list">
                            <li>
                                <span>Monday - Friday</span>
                                <span>9:00 AM - 6:00 PM</span>
                            </li>
                            <li>
                                <span>Saturday</span>
                                <span>10:00 AM - 4:00 PM</span>
                            </li>
                            <li>
                                <span>Sunday</span>
                                <span>Closed</span>
                            </li>
                            <li>
                                <span>Emergency Support</span>
                                <span>24/7 Available</span>
                            </li>
                        </ul>
                        
                        <div class="mt-4 pt-3 border-top">
                            <p class="text-muted small mb-2">
                                <i class="fas fa-info-circle me-1"></i>
                                For urgent legal matters, emergency support is available 24/7.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Frequently Asked Questions</h2>
                <p class="lead text-muted">Find quick answers to common questions</p>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How quickly will I receive a response?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We typically respond to all inquiries within 24 hours during business days. Urgent matters receive priority attention.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Do you offer free consultations?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer a free 30-minute initial consultation to understand your needs and discuss how our services can help you.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="accordion" id="faqAccordion2">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What information should I provide?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion2">
                                <div class="accordion-body">
                                    Please provide your name, contact information, and a brief description of your legal needs. The more details you provide, the better we can assist you.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Is my information secure?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion2">
                                <div class="accordion-body">
                                    Yes, we use enterprise-grade encryption and follow strict data protection protocols to ensure your information remains confidential and secure.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>