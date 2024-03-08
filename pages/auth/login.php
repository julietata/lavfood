<?php

session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

if (isset($_COOKIE['remember_user'])) {
    header('Location: /pages/home.php');
}

function csrf_token() {
    $token = "";

    if (!isset($_SESSION['login_token'])) {
        $_SESSION['login_token'] = bin2hex(random_bytes(16));
    }

    $token = $_SESSION['login_token'];
    return $token;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="relative bg-stone-800">
        <img src="../../asset/background.jpg" alt="" class="h-screen w-screen opacity-20">
        <div class="absolute top-0 min-h-screen w-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-amber-50 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="sm:mx-auto sm:w-full sm:max-w-md mb-8">
                        <img class="mx-auto h-20 w-auto" src="../../asset/LAvFood-logo.png">
                    </div>
                    <form class="space-y-4" action="../../controller/login-controller.php" method="POST">
                        <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">
                                Username
                            </label>
                            <div class="mt-1">
                                <input id="username" name="username" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-800 focus:border-green-800 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-800 focus:border-green-800 sm:text-sm">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                    Remember me
                                </label>
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
                            <button type="submit" name="login" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Sign in
                            </button>
                        </div>
                        <div class="flex justify-end">
                            <a href="../auth/register.php" class="text-sm italic font-semibold text-green-900">Don't have an account? Register Here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>