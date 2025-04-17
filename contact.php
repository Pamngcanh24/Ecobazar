<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css/contac.css">
</head>
<body>
    <?php include 'header.php';?>
    <div class="contact">

        <div class="breadcrumb">
            <a href="index.php" class="home-icon">
                <img src="img/home-1 1.png" alt="Home">
            </a> 
            <span> &gt; </span>
            <span class="current-page">Contact</span>
        </div>
    </div>

    <!-- lIÊN HJỆ -->
    <section class="contact-section">
        <div class="contact-details">
            <h2>Contact Info</h2>
            <p>
                <span class="icon">
                    <img src="imG/Map Pin.png" alt="Address Icon">
                </span>
                <strong>Address:</strong> 2715 Ash Dr. San Jose, South Dakota 83475
            </p>
            <p>
                <span class="icon">
                    <img src="img/Email.png" alt="Email Icon">
                </span>
                <strong>Email:</strong> Proxy@gmail.com, Help.proxy@gmail.com
            </p>
            <p>
                <span class="icon">
                    <img src="img/PhoneCall.png" alt="Phone Icon">
                </span>
                <strong>Phone:</strong> (219) 555-0114, (164) 333-0487
            </p>
        </div>
        <div class="form-wrapper">
            <h2>Just Say Hello!</h2>
            <form action="#" method="post">
                <div class="input-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="input-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Subject" required>
                </div>
                <div class="input-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </section>

    <!-- nhúng bản đồ -->
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3760.8176430909757!2d-98.88393932496122!3d19.50647988178884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1e7d99316a211%3A0xc06c16baeb5c63a0!2sEcoBazar%20Texcoco!5e0!3m2!1svi!2s!4v1743247959682!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      
   
    </div>

    <?php include 'footer.php';?> 
</body>
</html>
