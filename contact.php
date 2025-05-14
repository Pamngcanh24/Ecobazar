<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="css/contac.css">
</head>
<body>
    <?php include './includes/head.php';?>
    <div class="contact">

        <div class="breadcrumb-container">
            <div class="breadcrumb">
                <a href="homepage.php" class="home-icon" title="Home">
                <i class="fas fa-home" aria-hidden="true"></i>
                </a>
                <span> &gt; </span>
                <a style="color:  #218838;font-weight: bold;" >Contact</a>
            </div>
        </div>
    </div>    

    <!-- lIÊN HJỆ -->
    <div class="contact-container">
  
  <!-- Contact Info Left -->
  <div class="contact-card">
  <div class="contact-item">
    <img src="./img/Map Pin.png" alt="Location Icon" class="icon">
    <p>2715 Ash Dr. San Jose, South<br> Dakota 83475</p>
  </div>
  
  <hr>

  <div class="contact-item">
    <img src="./img/Email.png" alt="Email Icon" class="icon">
    <p>Proxy@gmail.com<br>Help.proxy@gmail.com</p>
  </div>
  
  <hr>

  <div class="contact-item">
    <img src="./img/PhoneCall.png" alt="Phone Icon" class="icon">
    <p>(219) 555-0114<br>(164) 333-0487</p>
  </div>
</div>


  <!-- Contact Form Right -->
  <div class="contact-form-container">
    <h2>Just Say Hello!</h2>
    <p>Do you fancy saying hi to me or you want to get started with your project and you need my help? Feel free to contact me.</p>

    <form class="contact-form">
      <div class="form-row">
        <input type="text" placeholder="Template Cookie" required>
        <input type="email" placeholder="zakirsoft@gmail.com" required>
      </div>

      <input type="text" placeholder="Hello!" required>
      <input type="text" placeholder="Subjects">

      <button type="submit">Send Message</button>
    </form>
  </div>

</div>


    <!-- nhúng bản đồ -->
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3760.8176430909757!2d-98.88393932496122!3d19.50647988178884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1e7d99316a211%3A0xc06c16baeb5c63a0!2sEcoBazar%20Texcoco!5e0!3m2!1svi!2s!4v1743247959682!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      
   
    </div>

    <?php include './includes/footer.php';?> 
</body>
</html>
