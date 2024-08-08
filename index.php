<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Donuts</title>
</head>
<body>
     <!-- Nav Container -->
     <div class="relative navigation-container z-50">
         <?php include 'navigation.php';  ?>
     </div>

     <!-- Content Container -->
        <div class="content-container flex justify-center items-center mx-8">
            <!-- Left Column -->
            <div class="w-1/2 flex flex-col justify-center items-start ml-4 gap-5">
                <h6 class="hero-h6 font-sans text-2xl text-justify indent-10">
                After countless iterations, we've perfected the art of crafting the ultimate donut. 
                By using Swiss milk and globally sourced, fresh ingredients, we create each donut from 
                scratch to deliver a truly irresistible, melt-in-your-mouth experience. Every bite of 
                our light, fluffy donuts promises a delightful taste sensation that captures the essence 
                of gourmet indulgence.
                </h6>
                <a href="cart.php">
                    <button class="hero-button font-sans text-lg" >
                        Order now!
                    </button>
                </a>
            </div>
            <!-- Right Column -->
            <div class="w-1/2 flex justify-center items-center ">
                <img src="img/logo/hero-img.svg" class="hero-image w-10/12">
            </div>
        </div>
</body>
</html>