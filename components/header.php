<header class="header">

<section class="flex">

    <a href="add-product.php" class="logo">LIG-IONS</a>

    <nav class="navbar">
        <a href="add-product.php">Add Product</a>
        <a href="view-products.php">View Products</a>
        <a href="Orders.php">Orders</a>
        <?php
        $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE
        user_id =?");
        $count_cart_items->execute([$user_id]);
        $total_cart_items = $count_cart_items->rowCount();
        ?>
        <a href="shopping-cart.php">Cart<span><?= $total_cart_items;?></span></a>

    </nav>

    <div id="menu-btn" class="fas fa-bars"></div>

</section>

</header>