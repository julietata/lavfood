<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();
$role = $data['UserRoleID'];

if ($role != 5) {
    header("Location: /pages/home.php");
}

$query = "SELECT * FROM User WHERE Username != 'admin'";
$prepared_statement = $conn->prepare($query);
$prepared_statement->execute();
$datas = $prepared_statement->get_result();

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
    <title>Manage User | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="px-4 py-4 flex-auto">
            <div class="flex items-center justify-between w-full mb-8">
                <div class="flex items-center">
                    <p class="font-bold text-2xl text-stone-800">Manage Users</p>
                    <div class="rounded-full bg-green-800 px-4 py-1 mx-4">
                        <p class="text-green-100 text-xs font-semibold"><?= $datas->num_rows ?> Active Users</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <button class="bg-stone-800 text-amber-50 flex items-center px-4 py-2 rounded-md" id="insert_button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-amber-50 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-semibold">Add New User</p>
                    </button>
                </div>
            </div>
            <div>
                <?php while ($row = $datas->fetch_assoc()) { ?>
                    <div class="my-4 bg-amber-100/75 rounded-md drop-shadow flex items-center justify-between px-4 py-2">
                        <div class="flex">
                            <?php if ($row['UserGender'] == "male") { ?>
                                <img class="w-12 h-12 rounded-full lg:w-16 lg:h-16 mr-4" src="https://avataaars.io/?avatarStyle=Transparent&amp;topType=ShortHairShortFlat&amp;accessoriesType=Blank&amp;hairColor=Black&amp;facialHairType=Blank&amp;clotheType=CollarSweater&amp;clotheColor=Black&amp;eyeType=Default&amp;eyebrowType=RaisedExcitedNatural&amp;mouthType=Twinkle&amp;skinColor=Light">
                            <?php } else { ?>
                                <img class="w-12 h-12 rounded-full lg:w-16 lg:h-16 mr-4" src="https://avataaars.io/?avatarStyle=Transparent&amp;topType=LongHairStraight2&amp;accessoriesType=Blank&amp;hairColor=Black&amp;facialHairType=Blank&amp;clotheType=GraphicShirt&amp;clotheColor=Pink&amp;graphicType=Bear&amp;eyeType=Default&amp;eyebrowType=DefaultNatural&amp;mouthType=Twinkle&amp;skinColor=Light">
                            <?php } ?>
                            <div class="text-stone-800">
                                <p class="font-semibold text-lg"><?= $row['Username'] ?></p>
                                <p><?= $row['UserEmail'] ?></p>
                            </div>
                        </div>
                        <form action="/controller/change-role-controller.php" method="POST" id="manage_user_role-<?= $row['UserID'] ?>" autocomplete="off">
                            <?php if ($row['UserRoleID'] == 1) { ?>
                                <div class="rounded-full border-2 border-green-800 px-3 py-2 w-32">
                                    <p class="text-green-800 font-semibold">Customer</p>
                                </div>
                            <?php } else { ?>
                                <div class="w-32 border-2 border-green-800 rounded-full shadow-sm placeholder-gray-400 sm:text-sm relative">
                                    <select name="roles" id="roles-<?= $row['UserID'] ?>" class=" z-10 appearance-none w-full px-3 py-2 rounded-full focus:outline-none focus:ring-green-500 focus:border-green-500 bg-amber-100/0 font-semibold text-green-800 text-base">
                                        <option value="2" <?= $row['UserRoleID'] == 2 ? "selected" : "" ?>>Cook</option>
                                        <option value="3" <?= $row['UserRoleID'] == 3 ? "selected" : "" ?>>Courier</option>
                                        <option value="4" <?= $row['UserRoleID'] == 4 ? "selected" : "" ?>>Supervisor</option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-green-800 absolute right-0 top-1/3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            <?php } ?>
                            <input type="hidden" value="<?= $row['UserID'] ?>" name="userid" id="userid">
                        </form>
                        <script>
                            $('#roles-<?= $row['UserID'] ?>').on('change', function() {
                                $('#manage_user_role-<?= $row['UserID'] ?>').submit();
                            })
                        </script>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="<?= isset($_SESSION['error']) ? "" : "hidden" ?> fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="insert_modal">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/2 h-fit overflow-y-auto rounded-lg">
            <form class="space-y-2" action="/controller/register-controller.php" method="POST">
                <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                <p class="font-bold text-xl">New User Data</p>
                <div class="w-full">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="username" class="text-sm font-medium text-gray-700 w-32">
                            Username
                        </label>
                        <div class="flex-auto">
                            <input id="username" name="username" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="email" class="text-sm font-medium text-gray-700 w-32">
                        Email
                    </label>
                    <div class="flex-auto">
                        <input id="email" name="email" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="roles" class="text-sm font-medium text-gray-700 w-32">
                        Role
                    </label>
                    <div class="flex-auto relative border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                        <select id="roles" name="roles" type="text" class="appearance-none w-full px-3 py-2 rounded-md">
                            <option value="0" disabled selected>Choose User Role</option>
                            <option value="1">Customer</option>
                            <option value="2">Cook</option>
                            <option value="3">Courier</option>
                            <option value="4">Supervisor</option>
                            <option value="5">Admin</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute top-1/4 right-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="address" class="text-sm font-medium text-gray-700 w-32">
                        Address
                    </label>
                    <div class="flex-auto">
                        <textarea id="address" name="address" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="phonenumber" class="text-sm font-medium text-gray-700 w-32">
                        Phone Number
                    </label>
                    <div class="flex-auto flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            +62
                        </span>
                        <input id="phonenumber" name="phonenumber" type="tel" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-r-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="birthdate" class="block text-sm font-medium text-gray-700 w-32">
                        Birthdate
                    </label>
                    <div class="flex-auto">
                        <input id="birthdate" name="birthdate" type="date" max="<?= date('Y-m-d') ?>" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="gender" class="block text-sm font-medium text-gray-700 w-32">
                        Gender
                    </label>
                    <div class="flex-auto flex flex-row items-center">
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

                <div class="flex">
                    <button type="button" name="cancel_insert" id="cancel_insert" class="mt-4 mr-2 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" name="register" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
    $("#insert_button").click(function() {
        $("#insert_modal").removeClass("hidden");
    })
    $("#cancel_insert").click(function() {
        $("#insert_modal").addClass("hidden");
    })
</script>

</html>