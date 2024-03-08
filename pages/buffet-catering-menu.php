<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$limit = 6;
$searched = isset($_GET['food_search']) ? $_GET['food_search'] : "";
$category_chosen = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

$query_param = "&food_search=".$searched."&category=".$category_chosen;

if ($category_chosen == 0) {

    $parameter = "%" . $searched . "%";

    $query = "SELECT COUNT(FoodID) AS count_foodid FROM food WHERE FoodName LIKE ?";
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $parameter);
    $statement->execute();
    $result = $statement->get_result();
    $count = $result->fetch_assoc();
    $number_of_food = $count['count_foodid'];
    $number_of_pages = ceil($number_of_food / $limit);

    $query = "SELECT * FROM food WHERE FoodName LIKE ? ORDER BY FoodName ASC LIMIT ?, ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("sii", $parameter, $start_from, $limit);
    $prepared_statement->execute();
    $datas = $prepared_statement->get_result();
} else {

    $search_param = "%" . $searched . "%";

    $query = "SELECT COUNT(FoodID) AS count_foodid FROM food WHERE FoodName LIKE ? AND FoodCategoryID = ?";
    $statement = $conn->prepare($query);
    $statement->bind_param("si", $search_param, $category_chosen);
    $statement->execute();
    $result = $statement->get_result();
    $count = $result->fetch_assoc();
    $number_of_food = $count['count_foodid'];
    $number_of_pages = ceil($number_of_food / $limit);

    $query = "SELECT * FROM food WHERE FoodName LIKE ? AND FoodCategoryID = ? ORDER BY FoodName ASC LIMIT ?, ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("siii", $search_param, $category_chosen, $start_from, $limit);
    $prepared_statement->execute();
    $datas = $prepared_statement->get_result();
}

$foods = array();
while ($row = $datas->fetch_assoc()) {
    array_push($foods, $row);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buffet Menu | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="flex-auto w-3/4 relative">
            <div class="w-full flex justify-between px-4 py-4">
                <p class="font-bold text-2xl text-stone-800">Buffet Menu</p>
                <div class="flex justify-end flex-auto">
                    <div class="flex rounded-md shadow-sm mr-4">
                        <form action="/pages/buffet-catering-menu.php" method="GET" class="flex w-full" id="fetch_form">
                            <input id="food_search" name="food_search" placeholder="Search..." value="<?= isset($_GET['food_search']) ? $_GET['food_search'] : "" ?>" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-l-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <span class="inline-flex items-center px-3 rounded-r-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                <select name="category" id="category" class="bg-gray-50 outline-none px-2">
                                    <option value="0" <?= isset($_GET['category']) && $_GET['category'] == "0" ? "selected" : "" ?>>All</option>
                                    <option value="1" <?= isset($_GET['category']) && $_GET['category'] == "1" ? "selected" : "" ?>>Protein</option>
                                    <option value="2" <?= isset($_GET['category']) && $_GET['category'] == "2" ? "selected" : "" ?>>Vegetables</option>
                                    <option value="3" <?= isset($_GET['category']) && $_GET['category'] == "3" ? "selected" : "" ?>>Carbohidrate</option>
                                    <option value="4" <?= isset($_GET['category']) && $_GET['category'] == "4" ? "selected" : "" ?>>Fruit</option>
                                    <option value="5" <?= isset($_GET['category']) && $_GET['category'] == "5" ? "selected" : "" ?>>Dessert</option>
                                </select>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            <div class="w-full grid grid-cols-3 gap-4 my-6 px-4">
                <?php foreach ($foods as $f) { ?>
                    <div class="bg-amber-100 rounded-md drop-shadow relative pb-4">
                        <img src="<?= $f['FoodImage'] ?>" alt="" class="w-full rounded-t-md h-1/2 object-cover">
                        <p class="px-2 py-1 font-bold text-lg"><?= $f['FoodName'] ?></p>
                        <p class="px-2 py-1 font-semibold">Rp <?= $f['FoodPrice'] ?></p>
                        <p class="px-2 py-1 break-words w-full"><?= $f['FoodDescription'] ?></p>
                    </div>
                <?php } ?>
            </div>
            <div class="fixed bottom-0 w-3/4 bg-amber-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] - 1) > 0 ? $_GET['page'] - 1 : $_GET['page']) : 1).$query_param ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                        Previous
                    </a>
                    <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] + 1) <= $number_of_pages ? $_GET['page'] + 1 : $_GET['page']) : 1).$query_param ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium"><?= (($page - 1) * $limit) + 1 ?></span>
                            to
                            <span class="font-medium"><?= ($page * $limit) <= $number_of_food ? ($page * $limit) : $number_of_food ?></span>
                            of
                            <span class="font-medium"><?= $number_of_food ?></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] - 1) > 0 ? $_GET['page'] - 1 : $_GET['page']) : 1).$query_param ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <?php for ($i = 0; $i < $number_of_pages; $i++) { ?>
                                <a href="?page=<?= ($i + 1).$query_param ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <?= $i + 1 ?>
                                </a>
                            <?php } ?>
                            <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] + 1) <= $number_of_pages ? $_GET['page'] + 1 : $_GET['page']) : 1).$query_param ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
    $('#food_search').on('change', function() {
        $('#fetch_form').submit();
    })
    $('#category').on('change', function() {
        $('#fetch_form').submit();
    })
</script>

</html>