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

if (!isset($_SESSION['transaction_active'])) {
    $_SESSION['transaction_active'] = "buffet";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History | LAvFood</title>
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
                    <option selected>Buffet</option>
                    <option>Package</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="relative z-0 rounded-lg shadow flex divide-x divide-gray-200" aria-label="Tabs">
                    <div id="food_tab" class="cursor-pointer text-stone-800 rounded-l-lg group relative min-w-0 flex-1 overflow-hidden bg-amber-50 py-4 px-4 text-xl font-bold text-center hover:bg-amber-100 focus:z-10" aria-current="page">
                        <span>Buffet</span>
                        <span aria-hidden="true" id="food_active" class="bg-green-800 absolute inset-x-0 bottom-0 h-1 <?= $_SESSION['transaction_active'] == "buffet" ? "" : "hidden" ?>"></span>
                    </div>

                    <div id="package_tab" class="cursor-pointer text-stone-800 hover:text-gray-700 group relative min-w-0 flex-1 overflow-hidden bg-amber-50 py-4 px-4 text-xl font-bold text-center hover:bg-amber-100 focus:z-10">
                        <span>Package</span>
                        <span aria-hidden="true" id="package_active" class="bg-green-800 absolute inset-x-0 bottom-0 h-1 <?= $_SESSION['transaction_active'] == "package" ? "" : "hidden" ?>"></span>
                    </div>
                </nav>
            </div>
            <div class="m-4" id="buffet_container">

            </div>
            <div class="m-4" id="package_container">

            </div>
        </div>
    </div>
    <div class="hidden fixed top-0 w-full h-full bg-stone-800 bg-opacity-50 flex justify-center items-center" id="status_log">
        <div class="pt-4 py-8 px-8 sm:px-8 bg-amber-50 drop-shadow w-1/3 h-fit overflow-y-auto rounded-lg">
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button type="button" id="close_log" class="rounded-md text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="font-bold text-xl py-4">Status Log</p>
            <div id="log_items">

            </div>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
    var item = 0;

    function load_buffet_transaction(lastid) {
        $.ajax({
            type: "POST",
            url: "/controller/load-buffet-trans-controller.php",
            data: {
                lastid: lastid
            },
            success: function(result) {
                const buffet_trans = JSON.parse(result);
                for (let i = 0; i < buffet_trans.length; i++) {
                    let buffet_list = $(`
                    <div class="w-full rounded-md bg-amber-100/75 px-3 py-3 mb-4">
                    <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg">${buffet_trans[i].receivedate}</p>
                    </div>
                    <div class="flex items-center">
                        <div class="border border-2 border-stone-800 rounded-md px-2 py-1">
                        <p class="text-stone-800 font-bold">${buffet_trans[i].currentstatus}</p>
                        </div>
                        <div id="action-${buffet_trans[i].transactionid}" class="flex"></div>
                    </div>
                    </div>
                    <div id="detail_div" class="w-full"></div>
                    <div class="flex justify-between border-t border-amber-200 w-full mt-2 pt-2">
                    <p class="font-bold text-lg">Total Price</p>    
                    <p class="font-bold text-lg">Rp ${buffet_trans[i].price}</p>
                    </div>
                    </div>
                    `);

                    for (let j = 0; j < buffet_trans[i].detail.length; j++) {
                        let buffet_detail = $(`
                        <div class="flex justify-between border-t border-amber-200 w-full mt-2 pt-2">
                        <p>${buffet_trans[i].detail[j].FoodName}</p>
                        <p>${buffet_trans[i].detail[j].Quantity}</p>
                        </div>
                        `)
                        buffet_list.find('#detail_div').append(buffet_detail);
                    }

                    const userrole = <?= $role ?>;

                    if (userrole == 1 && buffet_trans[i].currentstatus == "Unpaid") {
                        let pay_button = $(`
                        <form method="POST" action="/controller/notify-payment-controller.php">
                        <input type="hidden" value="${buffet_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${buffet_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="notify_payment" id="notify_payment-${buffet_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold ml-4">
                        Notify Payment
                        </button>
                        </form>
                        `)
                        buffet_list.find(`#action-${buffet_trans[i].transactionid}`).append(pay_button);
                    } else if (userrole == 1 && buffet_trans[i].currentstatus == "On Process") {
                        let finish_button = $(`
                        <form method="POST" action="/controller/receive-packet-controller.php">
                        <input type="hidden" value="${buffet_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${buffet_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="receive" id="receive-${buffet_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold w-40 ml-4">
                        Packet Received
                        </button>
                        </form>
                        `)
                        buffet_list.find(`#action-${buffet_trans[i].transactionid}`).append(finish_button);
                    } else if (userrole == 5 && buffet_trans[i].currentstatus == "Unpaid") {
                        let verify_payment = $(`
                        <form method="POST" action="/controller/verify-payment-controller.php">
                        <input type="hidden" value="${buffet_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${buffet_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="verify_payment" id="verify_payment-${buffet_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold w-40 ml-4">
                        Payment Verified
                        </button>
                        </form>
                        `)
                        buffet_list.find(`#action-${buffet_trans[i].transactionid}`).append(verify_payment);
                    }

                    if (userrole == 4 || userrole == 5) {
                        let log_btn = $(`
                        <button class="px-2 py-2 bg-green-800 rounded-md mx-2" id="show_log-${buffet_trans[i].transactionid}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-amber-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        </button>
                        `)

                        buffet_list.find(`#action-${buffet_trans[i].transactionid}`).append(log_btn);

                        log_btn.click(function() {
                            $('#log_items').empty();
                            for (let j = 0; j < buffet_trans[i].status.length; j++) {
                                let item = $(`
                                <div class="flex justify-between py-1">
                                <p>${buffet_trans[i].status[j].Status}</p>
                                <p>${buffet_trans[i].status[j].Date}</p>
                                </div>
                                `)
                                $('#log_items').append(item);
                            }
                            $('#status_log').removeClass('hidden');
                        })

                    }

                    $('#buffet_container').append(buffet_list);
                }
            }
        })
    }

    function load_package_transaction(lastid) {
        $.ajax({
            type: "POST",
            url: "/controller/load-package-tran-controller.php",
            data: {
                lastid: lastid
            },
            success: function(result) {
                const package_trans = JSON.parse(result);
                for (let i = 0; i < package_trans.length; i++) {
                    let package_list = $(`
                    <div class="w-full rounded-md bg-amber-100/75 px-3 py-3 mb-4">
                    <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg">${package_trans[i].receivedate}</p>
                    </div>
                    <div class="flex items-center">
                        <div class="border border-2 border-stone-800 rounded-md px-2 py-1">
                        <p class="text-stone-800 font-bold">${package_trans[i].currentstatus}</p>
                        </div>
                        <div id="action-${package_trans[i].transactionid}" class="flex"></div>
                    </div>
                    </div>
                    <div id="detail_div" class="w-full"></div>
                    <div class="flex justify-between border-t border-amber-200 w-full mt-2 pt-2">
                    <p class="font-bold text-lg">Total Price</p>    
                    <p class="font-bold text-lg">Rp ${package_trans[i].price}</p>
                        </div>
                    </div>
                    `);

                    for (let j = 0; j < package_trans[i].detail.length; j++) {
                        let package_detail = $(`
                        <div class="flex justify-between border-t border-amber-200 w-full mt-2 pt-2">
                        <p>${package_trans[i].detail[j].PackageName}</p>
                        <p>${package_trans[i].detail[j].Quantity}</p>
                        </div>
                        `)
                        package_list.find('#detail_div').append(package_detail);
                    }

                    const userrole = <?= $role ?>;

                    if (userrole == 1 && package_trans[i].currentstatus == "Unpaid") {
                        let pay_button = $(`
                        <form method="POST" action="/controller/notify-payment-controller.php">
                        <input type="hidden" value="${package_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${package_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="notify_payment" id="notify_payment-${package_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold ml-4">
                        Notify Payment
                        </button>
                        </form>
                        `)
                        package_list.find(`#action-${package_trans[i].transactionid}`).append(pay_button);
                    } else if (userrole == 1 && package_trans[i].currentstatus == "On Process") {
                        let finish_button = $(`
                        <form method="POST" action="/controller/receive-packet-controller.php">
                        <input type="hidden" value="${package_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${package_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="receive" id="receive-${package_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold w-40 ml-4">
                        Packet Received
                        </button>
                        </form>
                        `)
                        package_list.find(`#action-${package_trans[i].transactionid}`).append(finish_button);
                    } else if ((userrole == 4 || userrole == 5) && package_trans[i].currentstatus == "Unpaid") {
                        let verify_payment = $(`
                        <form method="POST" action="/controller/verify-payment-controller.php">
                        <input type="hidden" value="${package_trans[i].ordertype}" name="ordertype" id="ordertype">
                        <input type="hidden" value="${package_trans[i].transactionid}" name="transactionid" id="transactionid">
                        <button type="submit" name="verify_payment" id="verify_payment-${package_trans[i].transactionid}" class="rounded-md py-2 px-2 bg-green-800 text-amber-50 font-semibold w-40 ml-4">
                        Payment Verified
                        </button>
                        </form>
                        `)
                        package_list.find(`#action-${package_trans[i].transactionid}`).append(verify_payment);
                    }

                    if (userrole == 4 || userrole == 5) {
                        let log_btn = $(`
                        <button class="px-2 py-2 bg-green-800 rounded-md mx-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-amber-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        </button>
                        `)
                        package_list.find(`#action-${package_trans[i].transactionid}`).append(log_btn);

                        log_btn.click(function() {
                            $('#log_items').empty();
                            for (let j = 0; j < package_trans[i].status.length; j++) {
                                let item = $(`
                                <div class="flex justify-between py-1">
                                <p>${package_trans[i].status[j].Status}</p>
                                <p>${package_trans[i].status[j].Date}</p>
                                </div>
                                `)
                                $('#log_items').append(item);
                            }
                            $('#status_log').removeClass('hidden');
                        })
                    }

                    $('#package_container').append(package_list);
                }
            }
        })
    }

    if ($('#food_active').hasClass('hidden') == false) {
        load_buffet_transaction(item);
    }

    if ($('#package_active').hasClass('hidden') == false) {
        load_package_transaction(item);
    }

    $('#close_log').click(function() {
        $('#status_log').addClass('hidden');
    })

    $(window).scroll(() => {
        if (window.scrollY + window.innerHeight >= document.body.offsetHeight - 1) {
            item += 6;
            if ($('#food_active').hasClass('hidden') == false) {
                load_buffet_transaction(item);
            }

            if ($('#package_active').hasClass('hidden') == false) {
                load_package_transaction(item);
            }
        }
    })

    $('#food_tab').click(function() {
        if ($('#food_active').hasClass('hidden') == false) {
            return
        } else {
            $('#food_active').removeClass('hidden');
            $('#package_active').addClass('hidden');
            $('#package_container').empty();
            item = 0;
            load_buffet_transaction(item);
        }
    })

    $('#package_tab').click(function() {
        if ($('#package_active').hasClass('hidden') == false) {
            return
        } else {
            $('#package_active').removeClass('hidden');
            $('#food_active').addClass('hidden');
            $('#buffet_container').empty();
            item = 0;
            load_package_transaction(item);
        }
    })
</script>

</html>

<?php
unset($_SESSION['transaction_active']);
?>