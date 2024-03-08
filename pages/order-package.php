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

if ($role != 1) {
    header("Location: /pages/home.php");
}

$query = "SELECT * FROM packageheader";
$prepared_statement = $conn->prepare($query);
$prepared_statement->execute();
$datas = $prepared_statement->get_result();

$package_list = array();

while ($row = $datas->fetch_assoc()) {
    $query_detail = "SELECT f.FoodID, FoodName, Quantity FROM packagedetail AS pd JOIN food AS f ON pd.FoodID = f.FoodID WHERE PackageID = ?";
    $detail_statement = $conn->prepare($query_detail);
    $detail_statement->bind_param("i", $row['PackageID']);
    $detail_statement->execute();
    $details = $detail_statement->get_result();
    $detail_list = array();
    while($detail_row = $details->fetch_assoc()) {
        array_push($detail_list, $detail_row);
    }
    $package = [
        "packageid" => $row['PackageID'],
        "packagename" => $row['PackageName'],
        "packageimage" => $row['PackageImage'],
        "packageprice" => $row['PackagePrice'],
        "packagecontents" => $detail_list
    ];
    array_push($package_list, $package);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Buffet | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-amber-50">
    <div class="w-full h-full px-8 py-8">
        <p class="text-stone-800 font-bold text-4xl">Order Package</p>
    </div>
    <div class="px-8 space-y-6">
        <form action="/controller/order-package-controller.php" method="POST" id="order_package_form">
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:py-6">
                <label for="receivedate" class="font-medium text-gray-700 w-32">
                    Receive Date
                </label>
                <div class="flex-auto">
                    <input id="receivedate" name="receivedate" type="date" min="<?= date('Y-m-d') ?>" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:py-6">
                <label for="delivery" class="font-medium text-gray-700 w-32">
                    Delivery Method
                </label>
                <div class="flex-auto relative border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                    <select id="delivery" name="delivery" class="appearance-none w-full px-3 py-2 rounded-md">
                        <option value="0" disabled selected>Choose Delivery Method</option>
                        <option value="1">Delivered By Courier</option>
                        <option value="2">Self Pick-Up</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute top-1/4 right-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            <div class="sm:items-start sm:border-t sm:border-gray-200 sm:pt-6 w-full">
                <div class="flex mb-4 w-full">
                    <div class="w-1/2">
                        <div class="w-full flex justify-center bg-amber-100 rounded-l-md py-2 font-semibold text-stone-800">
                            <p>Our Menu</p>
                        </div>
                        <div class="bg-amber-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">
                            <?php foreach ($package_list as $p) { ?>
                                <div class="py-1 flex justify-between items-center">
                                    <?= $p['packagename'] ?>
                                    <button type="button" id="<?= $p['packageid'] ?>" onclick="append_chosen_menu('<?= $p['packageid'] ?>', '<?= $p['packagename'] ?>', <?= $p['packageprice'] ?>)">
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
                            <p>Your Choice</p>
                        </div>
                        <div id="chosen_container" class="bg-green-100/50 px-2 w-full drop-shadow h-72 overflow-y-auto">

                        </div>
                    </div>
                    <input type="hidden" name="chosen_menu_list" id="chosen_menu_list">
                </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-b sm:border-gray-200 sm:py-6">
                <p class="font-bold text-xl text-stone-800 w-32">Total: </p>
                <p class="font-bold text-xl text-stone-800 w-32" id="total_price">Rp 0</p>
                <input type="hidden" name="price" id="price" value="0">
            </div>
            <?php
            if (isset($_SESSION["order_error"])) {
            ?>
                <div class="mt-2 p-3 rounded-md bg-red-50">
                    <div class="flex justify-center">
                        <h3 class="text-sm text-center font-medium text-red-800">
                            <?= $_SESSION["order_error"] ?>
                        </h3>
                    </div>
                </div>
            <?php
                unset($_SESSION["order_error"]);
            }
            ?>
            <div class="py-6">
                <div class="flex justify-end">
                    <a href="/pages/home.php" class="bg-white py-2 px-6 border border-gray-300 rounded-md shadow-sm text-lg font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="button" id="save_order" name="save_order" class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-lg font-semibold rounded-md text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="confirmation_modal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-amber-50 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" id="close_confirmation" class="rounded-md text-gray-400 hover:text-gray-500">
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
                            Process Order
                        </h3>
                        <div class="mt-2">
                            <p id="message" class="text-sm text-gray-500">
                                Are you sure you want to process this order? You can't change anything or cancel this order after confirmation, make sure you have everything right.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirm_order" name="confirm_order" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-800 text-base font-medium text-white hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Order
                    </button>
                    <button type="button" id="cancel_confirmation" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>

    $('#save_order').click(function() {
        $('#confirmation_modal').removeClass('hidden');
    })

    $('#close_confirmation').click(function() {
        $('#confirmation_modal').addClass('hidden');
    })

    $('#cancel_confirmation').click(function() {
        $('#confirmation_modal').addClass('hidden');
    })

    $('#confirm_order').click(function() {
        $('#order_package_form').submit();
    })

    var chosen_menu = [];
    var total_price = 0;
    var delivery_price = 0;

    $('#delivery').on('change', function() {
        if ($('#delivery').val() == "1") {
            delivery_price = 10000;
            total_price += parseInt(delivery_price);
        } else {
            total_price -= delivery_price;
            delivery_price = 0;
        }
        $('#total_price').text(`Rp ${total_price}`);
        $('#price').val(total_price);
    })

    function append_chosen_menu(packageid, packagename, packageprice) {
        if (chosen_menu.find((v) => v.packageid == packageid) != null) {
            return;
        }
        chosen_menu.push({
            packageid: packageid,
            packagename: packagename,
            packageprice: packageprice,
            quantity: 1
        });

        $('#chosen_menu_list').val(JSON.stringify(chosen_menu));

        fetch_chosen_menu();
    }

    function fetch_chosen_menu() {
        $('#chosen_container').empty();
        total_price = delivery_price;
        for (let i = 0; i < chosen_menu.length; i++) {
            const chosen_list = $(`
            <div class="my-1 px-2 py-2 bg-green-50 rounded-md flex justify-between items-center w-full">
            <p class="w-full font-semibold text-green-800">${chosen_menu[i].packagename}</p>
            <input type="number" name="quantity" id="quantity-${chosen_menu[i].packageid}" value="${chosen_menu[i].quantity}" class="w-14 px-2 py-1 rounded-md" min=1>
            </div>
            `)
            $('#chosen_container').append(chosen_list);
            chosen_list.find(`#quantity-${chosen_menu[i].packageid}`).on('change', function() {
                chosen_menu[i].quantity = $(`#quantity-${chosen_menu[i].packageid}`).val();
                if (parseInt($(`#quantity-${chosen_menu[i].packageid}`).val()) <= 0) {
                    chosen_menu.splice(i, 1);
                    fetch_chosen_menu();
                }
                $('#chosen_menu_list').val(JSON.stringify(chosen_menu));
                calculate_price();
            })
        }
        calculate_price();
    }

    function calculate_price() {
        for (let i = 0; i < chosen_menu.length; i++) {
            total_price += (chosen_menu[i].packageprice * chosen_menu[i].quantity);
        }
        $('#total_price').text(`Rp ${total_price}`);
        $('#price').val(total_price);
    }
</script>

</html>