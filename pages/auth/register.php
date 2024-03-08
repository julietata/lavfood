<?php

    if(isset($_COOKIE['remember_user'])) {
        header('Location: /pages/home.php');
    }

    function csrf_token() {
        $token = "";
    
        if (!isset($_SESSION['register_token'])) {
            $_SESSION['register_token'] = bin2hex(random_bytes(16));
        }
    
        $token = $_SESSION['register_token'];
        return $token;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php
    session_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';
    ?>
    <div class="flex">
        <div class="bg-amber-50 min-h-screen w-2/3 flex flex-col justify-center sm:px-6 overflow-y-scroll max-h-screen">
            <div class="py-10 px-8 sm:px-8">
                <form class="space-y-2 pt-10" action="../../controller/register-controller.php" method="POST">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                    <div class="w-full">
                        <div class="mr-1 space-y-1">
                            <label for="username" class="text-sm font-medium text-gray-700">
                                Username
                            </label>
                            <div class="mt-1">
                                <input id="username" name="username" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="w-full space-y-2">
                        <div class="mr-1 space-y-1">
                            <label for="password" class="text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="ml-1 space-y-1">
                            <label for="confirmpassword" class="text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <div class="mt-1">
                                <input id="confirmpassword" name="confirmpassword" type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="address" class="text-sm font-medium text-gray-700">
                            Address
                        </label>
                        <div class="mt-1">
                            <textarea id="address" name="address" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="phonenumber" class="text-sm font-medium text-gray-700">
                            Phone Number
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                +62
                            </span>
                            <input id="phonenumber" name="phonenumber" type="tel" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-r-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="birthdate" class="block text-sm font-medium text-gray-700">
                            Birthdate
                        </label>
                        <div class="mt-1">
                            <input id="birthdate" name="birthdate" type="date" max="<?= date('Y-m-d') ?>" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="gender" class="block text-sm font-medium text-gray-700">
                            Gender
                        </label>
                        <div class="mt-1 flex flex-row items-center">
                            <div class="mr-4 flex items-center">
                                <input id="male" name="gender" value="male" type="radio" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                <label for="male" class="ml-2 text-sm font-medium text-gray-700">
                                    Male
                                </label>
                            </div>
                            <div class="mr-4 flex items-center">
                                <input id="female" name="gender" value="female" type="radio" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                <label for="female" class="ml-2 text-sm font-medium text-gray-700">
                                    Female
                                </label>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (isset($_SESSION["error"])) {
                    ?>
                        <div class="p-3 rounded-md bg-red-50">
                            <div class="flex justify-center">
                                <h3 class="text-sm text-center font-medium text-red-800">
                                    <?= $_SESSION["error"] ?>
                                </h3>
                            </div>
                        </div>
                    <?php
                        unset($_SESSION["error"]);
                    }
                    ?>

                    <div>
                        <button type="submit" name="register" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Sign up
                        </button>
                    </div>
                    <div class="flex justify-end">
                        <a href="./login.php" class="text-sm italic font-semibold text-green-900">Already have an account? Login here</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="relative bg-stone-800">
            <img src="../../asset/background.jpg" alt="" class="h-screen w-screen opacity-20">
            <div class="absolute top-0 w-full h-full flex justify-center items-center">
                <img src="../../asset/LAvFood-logo.png" alt="">
            </div>
        </div>
    </div>
</body>

</html>