<?php

    $db_name = 'mysql:host=localhost;dbname=shop_db';
    $db_user_name = 'root';
    $db_user_pass = '';

    $conn = new PDO($db_name, $db_user_name, $db_user_pass);

    function create_unique_id(){
        $charecters ='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sharecters_length = strlen($charecters);
        $random = '';
        for ($i = 0; $i < 20; $i++){
            $random .= $charecters[mt_rand(0, $sharecters_length - 1)];
        }
        return $random;
    };

?>

 