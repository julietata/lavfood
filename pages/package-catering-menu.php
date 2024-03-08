<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$limit = 6;
$searched = isset($_GET['package_search']) ? $_GET['package_search'] : "";
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

$query_param = "&package_search=" . $searched;

$parameter = "%" . $searched . "%";

$query = "SELECT COUNT(PackageID) AS count_packageid FROM packageheader WHERE PackageName LIKE ?";
$statement = $conn->prepare($query);
$statement->bind_param("s", $parameter);
$statement->execute();
$result = $statement->get_result();
$count = $result->fetch_assoc();
$number_of_package = $count['count_packageid'];
$number_of_pages = ceil($number_of_package / $limit);

$query = "SELECT * FROM packageheader WHERE PackageName LIKE ? ORDER BY PackageName LIMIT ?, ?";
$parameter = "%" . $searched . "%";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("sii", $parameter, $start_from, $limit);
$prepared_statement->execute();
$datas = $prepared_statement->get_result();

$packages = array();
while ($row = $datas->fetch_assoc()) {
    $query_detail = "SELECT f.FoodID, FoodName, FoodPrice, Quantity FROM packagedetail AS pd JOIN food AS f ON pd.FoodID = f.FoodID WHERE PackageID = ?";
    $detail_statement = $conn->prepare($query_detail);
    $detail_statement->bind_param("i", $row['PackageID']);
    $detail_statement->execute();
    $details = $detail_statement->get_result();
    $detail_list = array();
    while ($detail_row = $details->fetch_assoc()) {
        array_push($detail_list, $detail_row);
    }
    $package = [
        'packageid' => $row['PackageID'],
        'packagename' => $row['PackageName'],
        'packageimage' => $row['PackageImage'],
        'packageprice' => $row['PackagePrice'],
        'packagecontents' => $detail_list
    ];

    array_push($packages, $package);
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
                <p class="font-bold text-2xl text-stone-800">Package Menu</p>
                <div class="flex justify-end flex-auto">
                    <div class="flex rounded-md shadow-sm mr-4 w-1/3">
                        <form action="/pages/package-catering-menu.php" method="GET" class="flex w-full" id="fetch_form">
                            <input id="food_search" name="package_search" placeholder="Search..." value="<?= isset($_GET['package_search']) ? $_GET['package_search'] : "" ?>" type="text" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-l-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <span class="inline-flex items-center px-3 rounded-r-md border border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                <button type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            <div class="w-full grid grid-cols-3 gap-4 my-6 px-4">
                <?php foreach ($packages as $p) { ?>
                    <div class="bg-amber-100 rounded-md drop-shadow relative pb-4">
                        <img src="<?= $p['packageimage'] ?>" alt="" class="w-full rounded-t-md h-1/2 object-cover">
                        <p class="px-2 py-1 font-bold text-lg"><?= $p['packagename'] ?></p>
                        <p class="px-2 py-1 font-semibold"><?= $p['packageprice'] ?></p>
                        <div id="content_container">
                            <?php foreach ($p['packagecontents'] as $c) { ?>
                            <div class="flex justify-between">
                                <p class="px-2 py-1 break-words w-7/8"><?= $c['FoodName'] ?></p>
                                <p class="px-2 py-1"><?= $c['Quantity'] ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="fixed bottom-0 w-3/4 bg-amber-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] - 1) > 0 ? $_GET['page'] - 1 : $_GET['page']) : 1) . $query_param ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                        Previous
                    </a>
                    <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] + 1) <= $number_of_pages ? $_GET['page'] + 1 : $_GET['page']) : 1) . $query_param ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium"><?= (($page - 1) * $limit) + 1 ?></span>
                            to
                            <span class="font-medium"><?= ($page * $limit) <= $number_of_package ? ($page * $limit) : $number_of_package ?></span>
                            of
                            <span class="font-medium"><?= $number_of_package ?></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] - 1) > 0 ? $_GET['page'] - 1 : $_GET['page']) : 1) . $query_param ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <?php for ($i = 0; $i < $number_of_pages; $i++) { ?>
                                <a href="?page=<?= ($i + 1) . $query_param ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <?= $i + 1 ?>
                                </a>
                            <?php } ?>
                            <a href="?page=<?= (isset($_GET['page']) ? (($_GET['page'] + 1) <= $number_of_pages ? $_GET['page'] + 1 : $_GET['page']) : 1) . $query_param ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
    $('#package_search').on('change', function() {
        $('#fetch_form').submit();
    })
</script>

</html>