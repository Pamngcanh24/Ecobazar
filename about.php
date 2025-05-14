<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
    <title>About</title>
    <link rel="stylesheet" href="css/about.css">
</head>
<body>

    <?php include './includes/head.php';?>
    
    <div class="about">
        <div class="breadcrumb-container">
            <div class="breadcrumb">
                <a href="homepage.php" class="home-icon" title="Home">
                <i class="fas fa-home" aria-hidden="true"></i>
                </a>
                <span> &gt; </span>
                <a style="color: #218838; font-weight: bold;">About</a>
            </div>
        </div>
<!-- contten1 -->
        <div class="about-content1">
            <div class="intro-section">
                <h1><span class="highlight">100% Trusted</span><br>Organic Food Store</h1>
                <p>
                Morbi porttitor ligula in nunc varius sagittis. Proin dui nisi, laoreet<br>
                efficitur ligula ut, fringilla tincidunt enim. Donec id nunc ac erat<br>
                ut tempor ac, cursus vitae eros. Cras quis ultricies elit. Proin ac <br>
                lectus arcu. Maecenas aliquet vel tellus at accumsan. Donec a <br>
                eros nonmassa vulputate ornare. Vivamus ornare commodo <br>
                ante, at commodo felis congue vitae.
                </p>
            </div>

                <div class="about-img1"></div> 

        </div> 

<!-- conttent2 -->
        <div class="about-content2">
            
                
            <div class="container2">
                <h1>100% Trusted <br><span>Organic Food Store</span></h1>
                <p style="opacity: 60%;"> 
                Pellentesque a ante vulputate leo porttitor luctus sed eget eros. Nulla et<br>
                rhoncus neque. Duis non diam eget est luctus tincidunt a a mi. Nulla eu<br>
                eros consequat tortor tincidunt feugiat. 
                </p>

                <div class="features">

                  <div class="feature-box">
                    <img class="img" src="./img/box1.png" alt="box">
                    <h3>100% Organic food</h3>
                    <p>100% healthy & fresh food</p>
                </div>

                <div class="feature-box">
                    <img class="img" src="./img/box2.png" alt="box">
                    <h3>Great Support 24/7</h3>
                    <p>Instant access to Contact</p>
                </div>

                <div class="feature-box">
                    <img class="img" src="./img/box3.png" alt="box">
                    <h3>Customer Feedback</h3>
                    <p>Our happy customers</p>
                </div>   

                <div class="feature-box">
                    <img class="img" src="./img/box4.png" alt="box">
                    <h3>100% Secure Payment</h3>
                    <p>We ensure your money is safe</p>
                </div>

                <div class="feature-box">
                    <img class="img" src="./img/box5.png" alt="box">
                    <h3>Free Shipping</h3>
                    <p>Free shipping with discount</p>
                </div>

                <div class="feature-box">
                    <img class="img" src="./img/box6.png" alt="box">
                    <h3>100% Organic Food</h3>
                    <p>100% healthy & fresh food</p>
                </div>
            </div>
               
            

              

            </div>  
            

        </div> 



<!-- 3 -->
        <div class="about-content3">
            
            <div class="content">
                <h1> We Delivered, You <br> Enjoy Your Order.</h1>
                <p class="desc">
                Ut suscipit egestas suscipit. Sed posuere pellentesque nunc,<br>
                ultrices consectetur velit dapibus eu. Mauris sollicitudin dignissim<br>
                diam, ac mattis eros accumsan rhoncus. Curabitur auctor<br> 
                bibendum nunc eget elementum.
                </p>
    
                <ul class="list">
                <li><span class="check"><img src="./img/Check.png" alt=""></span> Sed in metus pellentesque.</li>
                <li><span class="check"><img src="./img/Check.png" alt=""></span> Fusce et ex commodo, aliquam nulla efficitur, tempus lorem.</li>
                 <li><span class="check"><img src="./img/Check.png" alt=""></span> Maecenas ut nunc fringilla erat varius.</li>
                </ul>

                <a href="08shop.php" class="btn">Shop Now →</a>
            </div>
        </div>


<!-- 4 -->
        <section class="team-section">
            <div class="container4">
                 <h2>Our Awesome Team</h2>
                <p>Pellentesque a ante vulputate leo porttitor luctus sed eget eros. Nulla et rhoncus neque. Duis non diam eget est luctus tincidunt a a mi.</p>

                <div class="team-grid">
                <div class="team-member">
                <div class="member-photo" style="background-image: url('./img/pp1.png');">
                </div>
                    <h3>Jenny Wilson</h3>
                    <p>CEO & Founder</p>
                </div>

                <div class="team-member">
                <div class="member-photo" style="background-image: url('./img/pp2.png');"></div>
                    <h3>Jane Cooper</h3>
                    <p>Worker</p>
                </div>

                <div class="team-member">
                <div class="member-photo" style="background-image: url('./img/pp3.png');"></div>
                    <h3>Cody Fisher</h3>
                    <p>Security Guard</p>
                </div>

                <div class="team-member">
                <div class="member-photo" style="background-image: url('./img/pp4.png');"></div>
                    <h3>Robert Fox</h3>
                    <p>Senior Farmer Manager</p>
                </div>
                </div>
             </div>
        </section>

        <div class="about-content5"></div>

        


 
    </div>
    <?php include './includes/footer.php';?> 
</body>
</html>
