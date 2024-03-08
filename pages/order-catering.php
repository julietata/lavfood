<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';
error_reporting(0);

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();
$role = $data['UserRoleID'];

if ($role != 1) {
    header("Location: /pages/home.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="flex-auto">
            <div class="w-full px-4 pt-4 pb-2">
                <div class="w-full h-full relative">
                    <img src="/asset/buffet.jpg" alt="" class="w-full h-[calc(100vh-50vh-24px)] object-cover rounded-lg">
                    <a href="/pages/order-buffet.php" class="absolute top-0 w-full h-full bg-stone-800 bg-opacity-50 rounded-lg flex justify-center items-center">
                        <p class="text-amber-50 font-extrabold text-6xl">Buffet</p>
                    </a>
                </div>
            </div>
            <div class="w-full px-4 pt-2 pb-4">
                <div class="w-full h-full relative">
                    <img src="/asset/package.jpeg" alt="" class="w-full h-[calc(100vh-50vh-24px)] object-cover rounded-lg">
                    <a href="/pages/order-package.php" class="absolute top-0 w-full h-full bg-stone-800 bg-opacity-50 rounded-lg flex justify-center items-center">
                        <p class="text-amber-50 font-extrabold text-6xl">Package</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>