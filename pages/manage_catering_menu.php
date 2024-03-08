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

if ($role == 1 || $role == 3) {
    header("Location: /pages/home.php");
}

if (!isset($_SESSION['active'])) {
    $_SESSION['active'] = "food";
}

$query = "SELECT * FROM food";
$prepared_statement = $conn->prepare($query);
$prepared_statement->execute();
$datas = $prepared_statement->get_result();

$food_list = array();

while ($row = $datas->fetch_assoc()) {
    array_push($food_list, $row);
}

function csrf_token()
{
    $token = "";

    if (!isset($_SESSION['manage_token'])) {
        $_SESSION['manage_token'] = bin2hex(random_bytes(16));
    }

    $token = $_SESSION['manage_token'];
    return $token;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catering Menu | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="flex-auto w-3/4">
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select id="tabs" name="tabs" class="block w-full focus:ring-green-800 focus:border-green-800 border-gray-300 rounded-md bg-amber-50">
                    <option selected>Food</option>
                    <option>Package</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="relative z-0 rounded-lg shadow flex divide-x divide-gray-200" aria-label="Tabs">
                    <div id="food_tab" class="cursor-pointer text-stone-800 rounded-l-lg group relative min-w-0 flex-1 overflow-hidden bg-amber-50 py-4 px-4 text-xl font-bold text-center hover:bg-amber-100 focus:z-10" aria-current="page">
                        <span>Food</span>
                        <span aria-hidden="true" id="food_active" class="bg-green-800 absolute inset-x-0 bottom-0 h-1 <?= $_SESSION['active'] == "food" ? "" : "hidden" ?>"></span>
                    </div>

                    <div id="package_tab" class="cursor-pointer text-stone-800 hover:text-gray-700 group relative min-w-0 flex-1 overflow-hidden bg-amber-50 py-4 px-4 text-xl font-bold text-center hover:bg-amber-100 focus:z-10">
                        <span>Package</span>
                        <span aria-hidden="true" id="package_active" class="bg-green-800 absolute inset-x-0 bottom-0 h-1 <?= $_SESSION['active'] == "package" ? "" : "hidden" ?>"></span>
                    </div>
                </nav>
            </div>
            <div id="food_section" class="mt-6 px-6 flex justify-end w-full box-border <?= $_SESSION['active'] == "food" ? "" : "hidden" ?>">
                <div class="flex rounded-md shadow-sm mr-4 w-1/3">
                    <input id="food_search" name="food_search" placeholder="Search..." type="text" class="appearance-none px-3 w-full border border-gray-300 rounded-l-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <span class="inline-flex items-center px-3 rounded-r-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
                <button class="bg-stone-800 text-amber-50 flex items-center px-4 py-2 rounded-md" id="insert_food_button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-amber-50 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-semibold">Add New Food</p>
                </button>
            </div>
            <div id="package_section" class="mt-6 px-6 flex justify-end w-full box-border <?= $_SESSION['active'] == "package" ? "" : "hidden" ?>">
                <div class="flex rounded-md shadow-sm mr-4 w-1/3">
                    <input id="package_search" name="package_search" placeholder="Search..." type="text" class="appearance-none px-3 w-full border border-gray-300 rounded-l-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <span class="inline-flex items-center px-3 rounded-r-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
                <button class="bg-stone-800 text-amber-50 flex items-center px-4 py-2 rounded-md" id="insert_package_button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-amber-50 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-semibold">Add New Package</p>
                </button>
            </div>
            <div id="food_container" class="px-4 grid grid-cols-3 gap-4 my-6">
            </div>
        </div>
    </div>

    <div class="<?= isset($_SESSION['insert_food_error']) ? "" : "hidden" ?> fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="insert_food_modal">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/2 h-fit overflow-y-auto rounded-lg">
            <form class="space-y-2" action="/controller/insert-food-controller.php" method="POST" enctype="multipart/form-data">
                <p class="font-bold text-xl">New Food</p>
                <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                <div class="w-full">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="foodname" class="text-sm font-medium text-gray-700 w-32">
                            Food Name
                        </label>
                        <div class="flex-auto">
                            <input id="foodname" name="foodname" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="price" class="text-sm font-medium text-gray-700 w-32">
                        Food Price
                    </label>
                    <div class="flex-auto">
                        <input id="price" name="price" type="number" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="category" class="text-sm font-medium text-gray-700 w-32">
                        Category
                    </label>
                    <div class="flex-auto relative border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                        <select id="category" name="category" type="text" class="appearance-none w-full px-3 py-2 rounded-md">
                            <option value="0" disabled selected>Choose Food Category</option>
                            <option value="1">Protein</option>
                            <option value="2">Vegetables</option>
                            <option value="3">Carbohidrate</option>
                            <option value="4">Fruit</option>
                            <option value="5">Dessert</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute top-1/4 right-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="description" class="text-sm font-medium text-gray-700 w-32">
                        Description
                    </label>
                    <div class="flex-auto">
                        <textarea id="description" name="description" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="image" class="text-sm font-medium text-gray-700 w-32">
                        Food Image
                    </label>
                    <div class="flex-auto flex items-center">
                        <label class="cursor-pointer bg-white appearance-none px-3 py-2 w-fit border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            Choose Image
                            <input id="image" name="image" type="file" class="hidden">
                        </label>
                        <p id="filename" class="ml-2"></p>
                    </div>
                </div>

                <?php
                if (isset($_SESSION["insert_food_error"])) {
                ?>
                    <div class="p-3 rounded-md bg-red-50">
                        <div class="flex justify-center">
                            <h3 class="text-sm text-center font-medium text-red-800">
                                <?= $_SESSION["insert_food_error"] ?>
                            </h3>
                        </div>
                    </div>
                <?php
                    unset($_SESSION["insert_food_error"]);
                }
                ?>

                <div class="flex">
                    <button type="button" name="cancel_insert_food" id="cancel_insert_food" class="mt-4 mr-2 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" name="insert_food" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="<?= isset($_SESSION['insert_package_error']) ? "" : "hidden" ?> fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="insert_package_modal">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/2 h-fit overflow-y-auto rounded-lg">
            <form action="/controller/insert-package-controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                <p class="font-bold text-xl">New Package Set</p>
                <div class="w-full my-4">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="packagename" class="text-sm font-medium text-gray-700 w-32">
                            Package Name
                        </label>
                        <div class="flex-auto">
                            <input id="packagename" name="packagename" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="mb-6 flex items-center">
                    <label for="package_image" class="text-sm font-medium text-gray-700 w-32">
                        Package Image
                    </label>
                    <div class="flex-auto flex items-center">
                        <label class="cursor-pointer bg-white appearance-none px-3 py-2 w-fit border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            Choose Image
                            <input id="package_image" name="package_image" type="file" class="hidden">
                        </label>
                        <p id="package_filename" class="ml-2"></p>
                    </div>
                </div>

                <div class="w-full flex mb-4">
                    <div class="w-1/2">
                        <div class="w-full flex justify-center bg-amber-100 rounded-l-md py-2 font-semibold text-stone-800">
                            <p>Food Menu</p>
                        </div>
                        <div class="bg-amber-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">
                            <?php foreach ($food_list as $f) { ?>
                                <div class="py-1 flex justify-between items-center">
                                    <?= $f['FoodName'] ?>
                                    <button type="button" id="<?= $f['FoodID'] ?>" onclick="append_chosen_menu(<?= $f['FoodID'] ?>, '<?= $f['FoodName'] ?>', <?= $f['FoodPrice'] ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="flex-auto">
                        <div class="w-full flex justify-center bg-green-800 rounded-r-md py-2 font-semibold text-amber-50">
                            <p>Chosen Menu</p>
                        </div>
                        <div id="chosen_container" class="bg-green-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">

                        </div>
                    </div>
                </div>

                <input type="hidden" name="chosen_menu_list" id="chosen_menu_list">

                <div class="w-full my-4">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="packageprice" class="text-sm font-medium text-gray-700 w-32">
                            Package Price
                        </label>
                        <div class="flex-auto">
                            <input id="packageprice" name="packageprice" type="number" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_SESSION["insert_package_error"])) {
                ?>
                    <div class="p-3 rounded-md bg-red-50">
                        <div class="flex justify-center">
                            <h3 class="text-sm text-center font-medium text-red-800">
                                <?= $_SESSION["insert_package_error"] ?>
                            </h3>
                        </div>
                    </div>
                <?php
                    unset($_SESSION["insert_package_error"]);
                }
                ?>

                <div class="flex">
                    <button type="button" name="cancel_insert_package" id="cancel_insert_package" class="mt-4 mr-2 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" name="insert_package" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="hidden fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="update_food_modal">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/2 h-fit overflow-y-auto rounded-lg">
            <form action="" enctype="multipart/form-data" id="update_form">
                <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                <p class="font-bold text-xl">Update Food</p>
                <div class="w-full">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="update_foodname" class="text-sm font-medium text-gray-700 w-32">
                            Food Name
                        </label>
                        <div class="flex-auto">
                            <input id="update_foodname" name="update_foodname" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="update_price" class="text-sm font-medium text-gray-700 w-32">
                        Food Price
                    </label>
                    <div class="flex-auto">
                        <input id="update_price" name="update_price" type="number" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="update_category" class="text-sm font-medium text-gray-700 w-32">
                        Category
                    </label>
                    <div class="flex-auto relative border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                        <select id="update_category" name="update_category" type="text" class="appearance-none w-full px-3 py-2 rounded-md">
                            <option value="1">Protein</option>
                            <option value="2">Vegetables</option>
                            <option value="3">Carbohidrate</option>
                            <option value="4">Fruit</option>
                            <option value="5">Dessert</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute top-1/4 right-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="update_description" class="text-sm font-medium text-gray-700 w-32">
                        Description
                    </label>
                    <div class="flex-auto">
                        <textarea id="update_description" name="update_description" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div class="space-y-1 flex items-center">
                    <label for="update_image" class="text-sm font-medium text-gray-700 w-32">
                        Food Image
                    </label>
                    <div class="flex-auto flex items-center">
                        <label class="cursor-pointer bg-white appearance-none px-3 py-2 w-fit border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            Update Image
                            <input id="update_image" name="update_image" type="file" class="hidden">
                        </label>
                        <p id="update_filename" class="ml-2"></p>
                    </div>
                </div>

                <div class="mt-2 p-3 rounded-md bg-red-50 hidden" id="error_container">
                    <div class="flex justify-center">
                        <h3 class="text-sm text-center font-medium text-red-800" id="error_message">

                        </h3>
                    </div>
                </div>

                <input type="hidden" id="foodid" name="foodid">

                <div class="flex">
                    <button type="button" name="cancel_update_food" id="cancel_update_food" class="mt-4 mr-2 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" name="update_food" id="update_food" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="hidden fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="update_package_modal">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/2 h-fit overflow-y-auto rounded-lg">
            <form action="" id="update_package_form" enctype="multipart/form-data">
                <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">
                <p class="font-bold text-xl">Update Package Set</p>
                <div class="w-full my-4">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="update_package_name" class="text-sm font-medium text-gray-700 w-32">
                            Package Name
                        </label>
                        <div class="flex-auto">
                            <input id="update_package_name" name="update_package_name" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="mb-6 flex items-center">
                    <label for="update_package_image" class="text-sm font-medium text-gray-700 w-32">
                        Package Image
                    </label>
                    <div class="flex-auto flex items-center">
                        <label class="cursor-pointer bg-white appearance-none px-3 py-2 w-fit border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            Update Image
                            <input id="update_package_image" name="update_package_image" type="file" class="hidden">
                        </label>
                        <p id="update_package_filename" class="ml-2"></p>
                    </div>
                </div>

                <div class="w-full flex mb-4">
                    <div class="w-1/2">
                        <div class="w-full flex justify-center bg-amber-100 rounded-l-md py-2 font-semibold text-stone-800">
                            <p>Food Menu</p>
                        </div>
                        <div class="bg-amber-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">
                            <?php foreach ($food_list as $f) { ?>
                                <div class="py-1 flex justify-between items-center">
                                    <?= $f['FoodName'] ?>
                                    <button type="button" id="<?= $f['FoodID'] ?>" onclick="append_update_menu(<?= $f['FoodID'] ?>, '<?= $f['FoodName'] ?>', <?= $f['FoodPrice'] ?>, 1, <?= $f['FoodPrice'] ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="flex-auto">
                        <div class="w-full flex justify-center bg-green-800 rounded-r-md py-2 font-semibold text-amber-50">
                            <p>Chosen Menu</p>
                        </div>
                        <div id="update_chosen_container" class="bg-green-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">

                        </div>
                    </div>
                </div>

                <input type="hidden" name="update_chosen_menu_list" id="update_chosen_menu_list">

                <div class="w-full my-4">
                    <div class="mr-1 space-y-1 flex items-center">
                        <label for="update_package_price" class="text-sm font-medium text-gray-700 w-32">
                            Package Price
                        </label>
                        <div class="flex-auto">
                            <input id="update_package_price" name="update_package_price" type="number" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-md bg-red-50 hidden" id="update_error_container">
                    <div class="flex justify-center">
                        <h3 class="text-sm text-center font-medium text-red-800" id="update_error_message">

                        </h3>
                    </div>
                </div>

                <input type="hidden" id="update_packageid" name="update_packageid">

                <div class="flex">
                    <button type="button" name="cancel_update_package" id="cancel_update_package" class="mt-4 mr-2 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancel
                    </button>
                    <button type="submit" name="update_package" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="delete_food_modal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-amber-50 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" id="close_delete_food" class="rounded-md text-gray-400 hover:text-gray-500">
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
                            Delete Food
                        </h3>
                        <div class="mt-2">
                            <p id="message" class="text-sm text-gray-500">

                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="/controller/delete-food-controller.php">
                    <input type="hidden" name="tobedeleted_foodid" id="tobedeleted_foodid">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="delete_food" name="delete_food" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button" id="cancel_delete_food" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="delete_package_modal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-amber-50 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" id="close_delete_package" class="rounded-md text-gray-400 hover:text-gray-500">
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
                            Delete Package
                        </h3>
                        <div class="mt-2">
                            <p id="package_message" class="text-sm text-gray-500">

                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="/controller/delete-package-controller.php">
                    <input type="hidden" name="tobedeleted_packageid" id="tobedeleted_packageid">
                    <input type="hidden" name="token" id="token" value="<?= csrf_token() ?>">

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="delete_package" name="delete_package" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button" id="cancel_delete_package" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 sm:mt-0 sm:w-auto sm:text-sm">
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
    function fetch_food(searched_food) {
        $.ajax({
            type: "POST",
            url: "/controller/fetch-food-controller.php",
            data: {
                foodname: searched_food
            },
            success: function(result) {
                const foods = JSON.parse(result);
                $('#food_container').empty();
                for (let i = 0; i < foods.length; i++) {
                    var food_card = $(`
                    <div class="bg-amber-100 rounded-md drop-shadow relative pb-12" id="food-${foods[i].FoodID}">
                    <img src="${foods[i].FoodImage}" alt="" class="w-full rounded-t-md h-1/2 object-cover">
                    <p class="px-2 py-1 font-bold text-lg">${foods[i].FoodName}</p>
                    <p class="px-2 py-1 font-semibold">Rp ${foods[i].FoodPrice}</p>
                    <p class="px-2 py-1 break-words w-full">${foods[i].FoodDescription}</p>
                    <div class="flex w-full absolute bottom-0">
                    <button id="process_update-${foods[i].FoodID}" class="text-stone-800 bg-amber-200 font-semibold rounded-bl-md w-1/2 py-2">Update</button>
                    <button id="process_delete-${foods[i].FoodID}" class="bg-red-800 text-red-50 font-semibold rounded-br-md w-1/2 py-2">Delete</button>
                    </div>
                    </div>
                    `);
                    $('#food_container').append(food_card);
                    food_card.find(`#process_delete-${foods[i].FoodID}`).click(function() {
                        $('#delete_food_modal').removeClass('hidden');
                        $('#message').text(`Are you sure you want to delete ${foods[i].FoodName}?`);
                        $('#tobedeleted_foodid').val(`${foods[i].FoodID}`);
                        $('#close_delete_food').click(function() {
                            $('#delete_food_modal').addClass('hidden')
                        });
                        $('#cancel_delete_food').click(function() {
                            $('#delete_food_modal').addClass('hidden')
                        });
                    })
                    food_card.find(`#process_update-${foods[i].FoodID}`).click(function() {
                        $('#update_food_modal').removeClass('hidden');
                        $('#foodid').val(`${foods[i].FoodID}`);
                        $('#update_foodname').val(`${foods[i].FoodName}`);
                        $('#update_category').val(`${foods[i].FoodCategoryID}`);
                        $('#update_price').val(`${foods[i].FoodPrice}`);
                        $('#update_description').val(`${foods[i].FoodDescription}`);
                        $('#update_image').on('change', function(e) {
                            $('#update_filename').text(e.target.files[0].name);
                        })
                        $('#cancel_update_food').click(function() {
                            $('#update_food_modal').addClass('hidden');
                        })
                        $('#update_form').on('submit', function(e) {
                            e.preventDefault();
                            var data = new FormData(this);

                            $.ajax({
                                type: "POST",
                                url: "/controller/update-food-controller.php",
                                data: data,
                                cache: false,
                                processData: false,
                                contentType: false,
                                success: function(result) {
                                    const food = JSON.parse(result);
                                    if ("error" in food) {
                                        $('#error_container').removeClass('hidden');
                                        $('#error_message').text(`${food.error}`);
                                    } else {
                                        $('#error_container').addClass('hidden');
                                        $('#update_foodname').val(`${food.FoodName}`);
                                        $('#update_category').val(`${food.FoodCategoryID}`);
                                        $('#update_price').val(`${food.FoodPrice}`);
                                        $('#update_description').val(`${food.FoodDescription}`);
                                        $('#update_food_modal').addClass('hidden');
                                        fetch_food("");
                                    }
                                }
                            })
                        })
                    })
                }
            }
        })
    }

    function fetch_package(searched_package) {
        $.ajax({
            type: "POST",
            url: "/controller/fetch-package-controller.php",
            data: {
                packagename: searched_package
            },
            success: function(result) {
                const packages = JSON.parse(result);
                $('#food_container').empty();
                for (let i = 0; i < packages.length; i++) {
                    var package_card = $(`
                    <div class="bg-amber-100 rounded-md drop-shadow relative pb-16" id="food-${packages[i].packageid}">
                    <img src="${packages[i].packageimage}" alt="" class="w-full rounded-t-md h-1/2 object-cover">
                    <p class="px-2 py-1 font-bold text-lg">${packages[i].packagename}</p>
                    <p class="px-2 py-1 font-semibold">Rp ${packages[i].packageprice}</p>
                    <div id="content_container">
                    </div>
                    <div class="flex w-full absolute bottom-0">
                    <button id="process_update_package-${packages[i].packageid}" class="text-stone-800 bg-amber-200 font-semibold rounded-bl-md w-1/2 py-2">Update</button>
                    <button id="process_delete_package-${packages[i].packageid}" class="bg-red-800 text-red-50 font-semibold rounded-br-md w-1/2 py-2">Delete</button>
                    </div>
                    </div>
                    `);

                    for (let j = 0; j < packages[i].packagecontents.length; j++) {
                        const content = $(`
                        <div class="flex justify-between">
                        <p class="px-2 py-1 break-words w-7/8">${packages[i].packagecontents[j].FoodName}</p>
                        <p class="px-2 py-1">${packages[i].packagecontents[j].Quantity}</p>
                        </div>
                        `)
                        package_card.find('#content_container').append(content);
                    }

                    $('#food_container').append(package_card);

                    package_card.find(`#process_delete_package-${packages[i].packageid}`).click(function() {
                        $('#delete_package_modal').removeClass('hidden');
                        $('#package_message').text(`Are you sure you want to delete ${packages[i].packagename}?`);
                        $('#tobedeleted_packageid').val(`${packages[i].packageid}`);
                        $('#close_delete_package').click(function() {
                            $('#delete_package_modal').addClass('hidden')
                        });
                        $('#cancel_delete_package').click(function() {
                            $('#delete_package_modal').addClass('hidden')
                        });
                    })

                    package_card.find(`#process_update_package-${packages[i].packageid}`).click(function() {
                        $('#update_package_modal').removeClass('hidden');
                        $('#update_packageid').val(`${packages[i].packageid}`);
                        $('#update_package_name').val(`${packages[i].packagename}`);
                        $('#update_package_price').val(`${packages[i].packageprice}`);

                        for (let j = 0; j < packages[i].packagecontents.length; j++) {
                            append_update_menu(packages[i].packagecontents[j].FoodID, packages[i].packagecontents[j].FoodName, packages[i].packagecontents[j].FoodPrice, packages[i].packagecontents[j].Quantity, 0)
                        }

                        $('#update_package_image').on('change', function(e) {
                            $('#update_package_filename').text(e.target.files[0].name);
                        })

                        $('#cancel_update_package').click(function() {
                            $('#update_package_modal').addClass('hidden');
                        })

                        $('#update_package_form').on('submit', function(e) {
                            e.preventDefault();
                            var data = new FormData(this);

                            $.ajax({
                                type: "POST",
                                url: "/controller/update-package-controller.php",
                                data: data,
                                cache: false,
                                processData: false,
                                contentType: false,
                                success: function(result) {
                                    const package = JSON.parse(result);
                                    if ("error" in package) {
                                        $('#update_error_container').removeClass('hidden');
                                        $('#update_error_message').text(`${package.error}`);
                                    } else {
                                        $('#update_package_modal').addClass('hidden');
                                        fetch_package("");
                                    }
                                }
                            })
                        })
                    })
                }
            }
        })
    }

    var chosen_menu = [];
    var update_menu = [];

    function append_chosen_menu(foodid, foodname, foodprice) {
        if (chosen_menu.find((v) => v.foodid == foodid) != null) {
            return;
        }
        chosen_menu.push({
            foodid: foodid,
            foodname: foodname,
            foodprice: foodprice,
            quantity: "1"
        });

        $('#chosen_menu_list').val(JSON.stringify(chosen_menu));

        fetch_chosen_menu();
    }

    function append_update_menu(foodid, foodname, foodprice, qty, addprice) {
        if (update_menu.find((v) => v.foodid == foodid) != null) {
            return;
        }
        update_menu.push({
            foodid: foodid,
            foodname: foodname,
            foodprice: foodprice,
            quantity: qty,
            addprice: addprice
        });

        $('#update_chosen_menu_list').val(JSON.stringify(update_menu));

        fetch_update_menu();
    }

    function fetch_chosen_menu() {
        $('#chosen_container').empty();
        for (let i = 0; i < chosen_menu.length; i++) {
            const chosen_list = $(`
            <div class="py-1 flex justify-between items-center w-full">
            <p class="w-3/4">${chosen_menu[i].foodname}</p>
            <input type="number" name="quantity" id="quantity-${chosen_menu[i].foodid}" value="${chosen_menu[i].quantity}" class="flex-auto w-1/4 px-2 py-1 rounded-md" min=1>
            </div>
            `)
            $('#chosen_container').append(chosen_list);
            chosen_list.find(`#quantity-${chosen_menu[i].foodid}`).on('change', function() {
                chosen_menu[i].quantity = $(`#quantity-${chosen_menu[i].foodid}`).val();
                if (parseInt($(`#quantity-${chosen_menu[i].foodid}`).val()) <= 0) {
                    chosen_menu.splice(i, 1);
                    fetch_chosen_menu();
                }
                $('#chosen_menu_list').val(JSON.stringify(chosen_menu));
                calculate_price();
            })
        }
        calculate_price();
    }

    function fetch_update_menu() {
        $('#update_chosen_container').empty();
        var total_price = parseInt($('#update_package_price').val());
        for (let i = 0; i < update_menu.length; i++) {
            const chosen_list = $(`
            <div class="py-1 flex justify-between items-center w-full">
            <p class="w-3/4">${update_menu[i].foodname}</p>
            <input type="number" name="quantity" id="quantity-${update_menu[i].foodid}" value="${update_menu[i].quantity}" class="flex-auto w-1/4 px-2 py-1 rounded-md" min=1>
            </div>
            `)
            $('#update_chosen_container').append(chosen_list);
            chosen_list.find(`#quantity-${update_menu[i].foodid}`).on('change', function() {
                var difference = parseInt($(`#quantity-${update_menu[i].foodid}`).val()) - parseInt(update_menu[i].quantity);
                update_menu[i].quantity = $(`#quantity-${update_menu[i].foodid}`).val();
                if (parseInt($(`#quantity-${update_menu[i].foodid}`).val()) <= 0) {
                    if (update_menu[i].addprice == 0) {
                        total_price -= update_menu[i].foodprice * 80 / 100;
                    } else {
                        total_price -= update_menu[i].addprice * 80 / 100;
                    }
                    update_menu.splice(i, 1);
                    fetch_update_menu();
                } else {
                    temp_addprice = update_menu[i].addprice;
                    update_menu[i].addprice += parseInt(update_menu[i].foodprice) * difference;
                    total_price += (parseInt(update_menu[i].addprice) - parseInt(temp_addprice)) * 80 / 100;
                }
                $('#update_chosen_menu_list').val(JSON.stringify(update_menu));
                $('#update_package_price').val(`${total_price}`);
            })
            total_price += parseInt(update_menu[i].addprice) * 80 / 100;
            $('#update_package_price').val(`${total_price}`);
        }
    }

    function calculate_price() {
        var total = 0;
        for (let i = 0; i < chosen_menu.length; i++) {
            total += (chosen_menu[i].foodprice * chosen_menu[i].quantity);
        }
        total = total * 80 / 100;
        $('#packageprice').val(total);
    }

    $('#food_search').on('keyup', function() {
        fetch_food($('#food_search').val());
    })

    $('#package_search').on('keyup', function() {
        fetch_package($('#package_search').val());
    })

    if ($('#food_active').hasClass('hidden') == false) {
        fetch_food("");
    }

    if ($('#package_active').hasClass('hidden') == false) {
        fetch_package("");
    }

    $('#food_tab').click(function() {
        if ($('#food_active').hasClass('hidden') == false) {
            return
        } else {
            $('#food_active').removeClass('hidden');
            $('#package_active').addClass('hidden');
            $('#food_section').removeClass('hidden');
            $('#package_section').addClass('hidden');
            fetch_food("");
        }
    })
    $('#package_tab').click(function() {
        if ($('#package_active').hasClass('hidden') == false) {
            return
        } else {
            $('#package_active').removeClass('hidden');
            $('#food_active').addClass('hidden');
            $('#package_section').removeClass('hidden');
            $('#food_section').addClass('hidden');
            fetch_package();
        }
    })
    $('#insert_food_button').click(function() {
        $('#insert_food_modal').removeClass('hidden');
    })
    $('#insert_package_button').click(function() {
        $('#insert_package_modal').removeClass('hidden');
    })
    $('#cancel_insert_food').click(function() {
        $('#insert_food_modal').addClass('hidden');
    })
    $('#cancel_insert_package').click(function() {
        $('#insert_package_modal').addClass('hidden');
    })
    $('#image').on('change', function(e) {
        $('#filename').text(e.target.files[0].name);
    })
    $('#package_image').on('change', function(e) {
        $('#package_filename').text(e.target.files[0].name);
    })
</script>

</html>

<?php
unset($_SESSION['active']);
?>