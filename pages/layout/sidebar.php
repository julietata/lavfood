<?php
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();
$role = $data['UserRoleID'];

?>

<div class="h-screen w-1/4 bg-stone-800 px-4 py-8 fixed top-0">
    <div class="flex items-center justify-center mb-6">
        <img src="../../asset/LAvFood-logo.png" alt="" class="w-32">
    </div>
    <a href="/pages/home.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md <?= strpos($_SERVER['REQUEST_URI'], "home") ? "bg-stone-900" : "" ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>
        <p class="text-amber-50 font-semibold text-lg ml-2">Home</p>
    </a>
    <div class="flex items-center justify-between py-3 hover:bg-stone-700 px-2 rounded-md cursor-pointer" id="dropdown">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
            </svg>
            <p class="text-amber-50 font-semibold text-lg ml-2">Catering Menu</p>
        </div>
        <div class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-amber-50" id="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
    <div class="pl-8 hidden" id="menu-items">
        <a href="/pages/buffet-catering-menu.php" class="flex items-center py-2 hover:bg-stone-700 px-2 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
            </svg>
            <p class="text-amber-50 font-semibold ml-2">Buffet</p>
        </a>
        <a href="/pages/package-catering-menu.php" class="flex items-center py-2 hover:bg-stone-700 px-2 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <p class="text-amber-50 font-semibold ml-2">Package</p>
        </a>
    </div>
    <?php if ($role == 1) { ?>
        <a href="/pages/order-catering.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
            </svg>
            <p class="text-amber-50 font-semibold text-lg ml-2">Order Catering</p>
        </a>
    <?php } ?>
    <a href="/pages/order-history.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
        </svg>
        <p class="text-amber-50 font-semibold text-lg ml-2">Order History</p>
    </a>
    <?php if ($role == 2 || $role == 4 || $role == 5) { ?>
        <a href="/pages/manage_catering_menu.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md <?= strpos($_SERVER['REQUEST_URI'], "manage_catering_menu") ? "bg-stone-900" : "" ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-amber-50 font-semibold text-lg ml-2">Manage Catering Menu</p>
        </a>
    <?php } ?>
    <?php if ($role == 5) { ?>
        <a href="/pages/manage_user.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md <?= strpos($_SERVER['REQUEST_URI'], "manage_user") ? "bg-stone-900" : "" ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
            <p class="text-amber-50 font-semibold text-lg ml-2">Manage User</p>
        </a>
    <?php } ?>
    <a href="/pages/profile.php" class="flex items-center py-3 hover:bg-stone-700 px-2 rounded-md <?= strpos($_SERVER['REQUEST_URI'], "profile") ? "bg-stone-900" : "" ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-amber-50" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
        </svg>
        <p class="text-amber-50 font-semibold text-lg ml-2">Profile</p>
    </a>
    <a href="/controller/logout-controller.php" class="flex justify-end items-center py-3 absolute bottom-0 mb-2 w-[calc(100%-32px)] box-border hover:bg-stone-700 px-2 rounded-md">
        <p class="text-amber-50 font-semibold text-lg mr-2">Logout</p>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-amber-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
    </a>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
    $("#dropdown").click(function() {
        $("#menu-items").toggleClass("hidden");
        $("#chevron").toggleClass("rotate-180");
    })
</script>