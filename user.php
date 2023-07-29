<?php
 
 @include 'config.php';
 
 session_start();
 if(!isset($_SESSION['user_name'])){
     header('location:login.php');
 };
 
  ?> 

<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- font awsome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style-00.css">
    <title>admin-page</title>
 </head>
 <body>

    <div class="container">
        <div class="content">
            <h3>hi, <span>user</span></h3>
            <h1>welcome <span><?php echo $_SESSION['user_name']?></span></h1>
            <p>this is an user page</p>
            <a class="btn" href="login.php"><i class="fa-solid fa-user"></i> login</a>
            <a class="btn" href="register.php"><i class="fa-solid fa-user"></i> register</a>
         </div>
    </div>
 </body>
 </html>