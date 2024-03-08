<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();

function csrf_token()
{
    $token = "";

    if (!isset($_SESSION['profile_token'])) {
        $_SESSION['profile_token'] = bin2hex(random_bytes(16));
    }

    $token = $_SESSION['profile_token'];
    return $token;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="px-4 py-4 flex-auto box-border">
            <div class="bg-amber-100/75 drop-shadow rounded-lg py-4">
                <form class="space-y-2 px-8 py-3" action="/controller/update-profile-controller.php" method="POST">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                    <p class="font-bold text-2xl text-stone-800">Edit Profile</p>
                    <div class="w-full">
                        <div class="space-y-1 flex items-center">
                            <label for="username" class="text-sm font-medium text-gray-700 w-40">
                                Username
                            </label>
                            <div class="flex-auto">
                                <input value="<?= $data['Username'] ?>" <?= $data['UserRoleID'] == 5 ? "disabled" : "" ?> id="username" name="username" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 flex items-center">
                        <label for="email" class="text-sm font-medium text-gray-700 w-40">
                            Email
                        </label>
                        <div class="flex-auto">
                            <input id="email" value="<?= $data['UserEmail'] ?>" name="email" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-1 flex">
                        <label for="address" class="text-sm font-medium text-gray-700 w-40">
                            Address
                        </label>
                        <div class="flex-auto">
                            <textarea id="address" name="address" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?= $data['UserAddress'] ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-1 flex items-center">
                        <label for="phonenumber" class="text-sm font-medium text-gray-700 w-40">
                            Phone Number
                        </label>
                        <div class="flex-auto flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                +62
                            </span>
                            <input id="phonenumber" value="<?= $data['UserPhoneNumber'] ?>" name="phonenumber" type="tel" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-r-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <?php
                    if (isset($_SESSION["update_error"])) {
                    ?>
                        <div class="p-3 rounded-md bg-red-50">
                            <div class="flex justify-center">
                                <h3 class="text-sm text-center font-medium text-red-800">
                                    <?= $_SESSION["update_error"] ?>
                                </h3>
                            </div>
                        </div>
                    <?php
                        unset($_SESSION["update_error"]);
                    }
                    ?>

                    <div>
                        <button type="submit" name="update_profile" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-amber-100/75 drop-shadow rounded-lg py-4 mt-6">
                <form class="space-y-2 px-8 py-3" action="/controller/change-password-controller.php" method="POST">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                    <p class="font-bold text-2xl text-stone-800">Change Password</p>
                    <div class="w-full">
                        <div class="space-y-1 flex items-center">
                            <label for="oldpass" class="text-sm font-medium text-gray-700 w-40">
                                Old Password
                            </label>
                            <div class="flex-auto">
                                <input id="oldpass" name="oldpass" type="password" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 flex items-center">
                        <label for="newpass" class="text-sm font-medium text-gray-700 w-40">
                            New Password
                        </label>
                        <div class="flex-auto">
                            <input id="newpass" name="newpass" type="password" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="space-y-1 flex items-center">
                        <label for="connewpass" class="text-sm font-medium text-gray-700 w-40">
                            Confirm New Password
                        </label>
                        <div class="flex-auto">
                            <input id="connewpass" name="connewpass" type="password" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <?php
                    if (isset($_SESSION["changepass_error"])) {
                    ?>
                        <div class="p-3 rounded-md bg-red-50">
                            <div class="flex justify-center">
                                <h3 class="text-sm text-center font-medium text-red-800">
                                    <?= $_SESSION["changepass_error"] ?>
                                </h3>
                            </div>
                        </div>
                    <?php
                        unset($_SESSION["changepass_error"]);
                    }
                    ?>

                    <div>
                        <button type="submit" name="change_pass" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Change
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($data['UserRoleID'] != 5) { ?>

                <div>
                    <button type="submit" name="delete_account" id="delete_account" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-red-800 bg-red-100 border-2 border-red-800 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-200">
                        Delete Account
                    </button>
                </div>

            <?php } ?>

        </div>
    </div>
    <?php if (isset($_SESSION['success'])) { ?>
        <div aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start bg-stone-800/20" id="notif">
            <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
                <div class="max-w-sm w-full bg-green-50 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= $_SESSION['success'] ?>
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-800" id="close_notif">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
        unset($_SESSION['success']);
    }
    ?>

    <div class="fixed z-10 inset-0 overflow-y-auto <?= isset($_SESSION['delete_error']) ? "" : "hidden" ?>" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="delete_modal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-amber-50 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="/controller/delete-acc-controller.php" method="POST">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                    <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" id="close_delete" class="rounded-md text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete account
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete your account? All of your data will be permanently removed from our servers forever. This action cannot be undone.
                                </p>
                            </div>
                            <div class="my-4">
                                <input id="pass" name="pass" type="password" placeholder="Enter Password" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            <?php
                            if (isset($_SESSION["delete_error"])) {
                            ?>
                                <div class="p-3 rounded-md bg-red-50">
                                    <div class="flex justify-center">
                                        <h3 class="text-sm text-center font-medium text-red-800">
                                            <?= $_SESSION["delete_error"] ?>
                                        </h3>
                                    </div>
                                </div>
                            <?php
                                unset($_SESSION["delete_error"]);
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="delete" name="delete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button" id="cancel_delete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
    $("#close_notif").click(function() {
        $("#notif").addClass("hidden");
    })
    $("#delete_account").click(function() {
        $("#delete_modal").removeClass("hidden");
    })
    $("#cancel_delete").click(function() {
        $("#delete_modal").addClass("hidden");
    })
    $("#close_delete").click(function() {
        $("#delete_modal").addClass("hidden");
    })
</script>

</html>