<?php include 'header.php' ;?>
<?php include 'navbar.php' ;?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us  🎵</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: #222;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h2 {
            color: #222;
        }
        .highlight {
            color: #ff6600;
            font-weight: bold;
        }
        .contact-form {
            display: flex;
            flex-direction: column;
        }
        .contact-form label {
            font-weight: bold;
            margin-top: 10px;
        }
        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .contact-form button {
            margin-top: 15px;
            padding: 10px;
            background: #ff6600;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .contact-form button:hover {
            background: #e65c00;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background: #222;
            color: white;
            margin-top: 20px;
        }
        .map {
            margin-top: 20px;
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
        }
    </style>
</head>
<body>

<header>
    🎵 Get in Touch with <span class="highlight">Zeyd Music Shop</span>!
</header>

<div class="container">
    <div class="content">
        <h2>Contact Us</h2>
        <p>Have questions about our products, orders, or anything else? We're here to help! Fill out the form below or reach us at:</p>
        <p><strong>📍 Address:</strong> 123 Nairobi City</p>
        <p><strong>📞 Phone:</strong> +254 96-429-567</p>
        <p><strong>📧 Email:</strong> support@zeydmusicshop.com</p>

        <h2>Send Us a Message 🎸</h2>
        <form action="process_contact.php" method="POST" class="contact-form">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>

        <h2>Visit Us 🎶</h2>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63821.09836957215!2d36.756341531249994!3d-1.2826228000000037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1181bfb55125%3A0xc370b378b7f7a1a!2sMusicland%20Music%20shop!5e0!3m2!1sen!2ske!4v1742924804981!5m2!1sen!2ske" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
</div>

<!-- <div class="footer">
    &copy; <?php echo date("Y"); ?> Zeyd Music Shop. All Rights Reserved.
</div> -->

</body>
</html>
<?php include 'footer.php' ;?>