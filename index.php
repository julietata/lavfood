<?php
    if(isset($_COOKIE['remember_user'])) {
        header('Location: /pages/home.php');
    }
    error_reporting(0);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen">
    <div class="relative w-full h-full bg-[url('./asset/landing_image.jpg')]">
        <div class="absolute top-0 w-full h-full bg-stone-800/80">
            <?php include_once './pages/layout/header.php' ?>
            <div class="h-[calc(100%-96px)] flex flex-col justify-center items-center text-center px-16">
                <p class="font-extrabold text-5xl text-amber-100">Great Food, Great Service, Anytime Anywhere</p>
                <p class="text-zinc-100 mt-6 w-3/4">LAvFood catering service promises an extensive menu with exquisite taste, using only premium and fresh quality products. With the help of committed culinary experts, LAvFood always ready to serve you the best meal for your taste and tummy! You can trust LAvFood Catering as a food catering provider for your every day crave.</p>
                <a href="./pages/auth/register.php" class="mt-6 bg-amber-100 py-3 px-6 text-stone-900 font-semibold text-lg rounded-md mx-1 drop-shadow-xl">Order Now</a>
            </div>
        </div>
    </div>
</body>

</html>