<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

    <!-- font awsome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- header section starts -->
<?php include 'components/header.php'; ?>
<!-- header section ends -->

<!-- orders section starts -->

<section class="orders">

        <h1 class="heading">My Orders</h1>

        <div class="box-container">
            
        <?php
        $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY date DESC");
        $select_orders->execute([$user_id]);
        if($select_orders->rowCount() > 0){
            while($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)){
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                $select_products->execute([$fetch_order['product_id']]);
                if($select_products->rowCount()> 0){
                    while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
                 
                
        ?>
        <a href="view-order.php?get_id=<?= $fetch_order['id'];?>" class="box" <?php if($fetch_order['status'] == 'canceled'){echo 'style="border:.2rem solid var(--red)"';}?> >
            <p class="date"><i class="fas fa-calendar"></i><?= $fetch_order['date'];?></p>
            <img src="uploaded_files/<?= $fetch_product['image'];?>" class="image" alt="">
            <h3 class="name"><?= $fetch_product['name'];?></h3>
            <p class="price"><i class="fa-solid fa-dollar-sign"></i> <?= $fetch_order['price'];?> x <?= $fetch_order['qty'];?></p>
            <p class="status" style=" color:<?php if($fetch_order['status'] == 'canceled'){echo 'red';}elseif($fetch_order['status'] == 'delivered'){echo 'green';}else{echo 'orange';}; ?>"><?= $fetch_order['status'];?></p>
        </a>
        <?php
              }
            }
          }
        }else{
            echo '<p class="empty">orders not found!</p>';
        }

        ?>

        </div>

</section>

<!-- orders section ends -->
 

    













<!-- sweet alert cdn link -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>


<!-- custom js file link -->
<script src="js/main.js"></script>

<?php include 'components/alert.php'; ?>

</body>
</html>