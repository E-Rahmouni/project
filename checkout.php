<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
}

if(isset($_GET['get_id'])){
    $get_id = $_GET['get_id'];
}else{
    $get_id = '';
}

if(isset($_POST['place_order'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $method = $_POST['method'];
    $method = filter_var($method, FILTER_SANITIZE_STRING);
    $address_type = $_POST['address_type'];
    $address_type = filter_var($address_type, FILTER_SANITIZE_STRING);
    $address = $_POST['flat'].', '.$_POST['street'].', '.$_POST['city'].' - '.$_POST['country'].', '.$_POST['pin_code'];
    $address = filter_var($address, FILTER_SANITIZE_STRING);

    $verify_cart = $conn->prepare("SELECT *FROM `cart` WHERE user_id = ?");
    $verify_cart->execute([$user_id]);

    if(isset($_GET['get_id'])){

        $get_product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
        $get_product->execute([$_GET['get_id']]);
        if($get_product->rowCount() > 0){
            while($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)){
                $insert_order = $conn->prepare("INSERT INTO `orders`(id, user_id, name, number, email, address, address_type, method, product_id, price, qty) VALUE(?,?,?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([create_unique_id(), $user_id, $name, $number, $email, $address, $address_type, $method, $fetch_p['id'], $fetch_p['price'], 1]);
                header('location:orders.php');
            }
         }else{
            $warning_msg[] = 'Something went wrong!';
        }

    }elseif($verify_cart->rowCount() > 0){
        while($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)){
            $get_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? ");
            $get_price->execute([$f_cart['product_id']]);
            if($get_price->rowCount() > 0){
                while($f_price = $get_price->fetch(PDO::FETCH_ASSOC)){
                    $insert_order = $conn->prepare("INSERT INTO `orders`(id, user_id, name, number, email, address, address_type, method, product_id, price, qty) VALUE(?,?,?,?,?,?,?,?,?,?,?)");
                    $insert_order->execute([create_unique_id(), $user_id, $name, $number, $email, $address, $address_type, $method, $f_cart['id'], $f_price['price'], $f_cart['qty']]);
                    header('location:orders.php');      
                }

                if($insert_order){
                    $empty_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ? ");
                    $empty_cart->execute([$user_id]);

                }

            }else{
                $warning_msg[] = 'Something went wrong!'; 
            }
         }
    }else{
        $warning_msg[] = 'Your cart is empty!';
    }

} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- font awsome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- header section starts -->
<?php include 'components/header.php'; ?>
<!-- header section ends -->

<!-- checkout section starts -->

<section class="checkout">

        <h1 class="heading">Checkout Summary</h1>

        <div class="row">

            <form action="" method="POST">
                <h3>Billing Details</h3>
                <div class="flex">
                    <div class="box">
                    <p>Your Name <span>*</span></p>
                    <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="input">
                    <p>Your Email <span>*</span></p>
                    <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="input">
                    <p>Your Number <span>*</span></p>
                    <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="input">
                    <p>Payment Method <span>*</span></p>
                    <select name="method" class="input" required>
                        <option value="cash on delivery">cash on delivery</option>
                        <option value="net banking">net banking</option>
                        <option value="credit or debit card">credit or debit card</option>
                        <option value="UPI or RuPay">UPI or RuPay</option>
                    </select>
                    <p>Address Type <span>*</span></p>
                    <select name="address_type" class="input" required>
                        <option value="home">Home</option>
                        <option value="office">Office</option>
                    </select>
                    </div>
                    <div class="box">
                    <p>Address line 01  <span>*</span></p>
                    <input type="text" name="flat" required maxlength="50" placeholder="e.g. flat no & building no." class="input">
                    <p>Address line 02  <span>*</span></p>
                    <input type="text" name="street" required maxlength="50" placeholder="e.g. street name" class="input">
                    <p>City name <span>*</span></p>
                    <input type="text" name="city" required maxlength="50" placeholder="enter your city name" class="input">
                    <p>Country name <span>*</span></p>
                    <input type="text" name="country" required maxlength="50" placeholder="enter your country name" class="input">
                    <p>Pin code <span>*</span></p>
                    <input type="number" name="pin_code" required maxlength="9" min="0" max="999999" placeholder="enter your  pin code" class="input">
                    </div>
                </div>
                <input type="submit" value="Place Order" name="place_order" class="btn">
            </form>
            
            <div class="summary">
                <p class="title">total items :</p>
                <?php
                $grand_total = 0;
                if($get_id != ''){
                    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                    $select_product->execute([$get_id]);
                    if($select_product->rowCount() > 0){
                        while ($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
                            $grand_total = $fetch_product['price'];
                ?>
                <div class="flex">
                            <img src="uploaded_files/<?= $fetch_product['image']; ?>" alt="">
                            <div>
                                <h3 class="name"><?= $fetch_product['name']; ?></h3>
                                <p class="price"><i class="fa-solid fa-dollar-sign"></i> <?= $fetch_product['price'];?> x 1</p>
                            </div>
                </div>
                <?php
                
                            }
                        }else{
                            echo '<p class="empty">product was not found !</p>';
                        } 
                    }else{
                        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                        $select_cart->execute([$user_id]);
                        if($select_cart->rowCount() > 0){
                            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                            $select_p = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                            $select_p->execute([$fetch_cart['product_id']]);
                            if($select_p->rowCount() > 0){
                                while($f_product = $select_p->fetch(PDO::FETCH_ASSOC)){
                                    $sub_total = ($f_product['price'] * $fetch_cart['qty']);
                                    $grand_total += $sub_total;
                
                    ?> 
                <div class="flex">
                            <img src="uploaded_files/<?= $f_product['image']; ?>" alt="">
                            <div>
                                <h3 class="name"><?= $f_product['name']; ?></h3>
                                <p class="price"><i class="fa-solid fa-dollar-sign"></i> <?= $f_product['price'];?> x <?= $fetch_cart['qty'];?></p>
                            </div>
                </div>
                    <?php
                            }
                            }else{
                                echo '<p class="empty">product was not found!</p>';
                            }
                
                        }
                    }else{
                        echo '<p class="empty">your cart is empty!</p>';
                    }
                 } 
                ?> 

                <p class="grand-total">Grand Total : <span><i class="fa-solid fa-dollar-sign"></i> <?=$grand_total; ?></span></p>

            </div>

        </div>

</section>

<!-- checkout section ends -->

 

    










<!-- sweet alert cdn link -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>


<!-- custom js file link -->
<script src="js/main.js"></script>

<?php include 'components/alert.php'; ?>

</body>
</html>