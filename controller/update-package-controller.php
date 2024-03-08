<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['manage_token']) && $_SESSION['manage_token'] == $_POST['token']) {
    $packageid = htmlspecialchars($_POST['update_packageid']);
    $name = htmlspecialchars($_POST['update_package_name']);
    $price = htmlspecialchars($_POST['update_package_price']);
    $menus = $_POST['update_chosen_menu_list'];
    $menus = (array)json_decode($menus);

    $return_data = array("error"=>"");

    if (check_empty($name) || check_empty($price)) {
        $return_data['error'] = "All fields must be filled!";
        echo json_encode($return_data);
        die();
    } else if (sizeof($menus) == 0) {
        $return_data['error'] = "Please choose package content!";
        echo json_encode($return_data);
        die();
    } else if (!isset($_FILES) || $_FILES['update_package_image']['error'] > 0) {
        $query_header = "UPDATE packageheader SET PackageName = ?, PackagePrice = ? WHERE PackageID = ?";

        $price = (int)$price;

        $header_statement = $conn->prepare($query_header);
        $header_statement->bind_param("sii", $name, $price, $packageid);
        $header_statement->execute();

        $delete_detail = "DELETE FROM packagedetail WHERE PackageID = ?";
        $delete_statement = $conn->prepare($delete_detail);
        $delete_statement->bind_param("i", $packageid);
        $delete_statement->execute();

        foreach ($menus as $menu) {
            $menu = (array)$menu;
            $query_detail = "INSERT INTO packagedetail VALUES (?, ?, ?)";
            $detail_statement = $conn->prepare($query_detail);
            $detail_statement->bind_param("iii", $packageid, $menu['foodid'], $menu['quantity']);
            $detail_statement->execute();
        }
    } else {
        $query_header = "UPDATE packageheader SET PackageName = ?, PackagePrice = ?, PackageImage = ? WHERE PackageID = ?";

        $type = $_FILES['update_package_image']['type'];

        if ($type != "image/jpg" && $type != "image/png" && $type != "image/jpeg") {
            $return_data = "Image must be in jpg/png/jpeg format!";
            return json_encode($return_data);
            die();
        }

        $image = file_get_contents($_FILES['update_package_image']['tmp_name']);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image);

        $price = (int)$price;

        $header_statement = $conn->prepare($query_header);
        $header_statement->bind_param("sisi", $name, $price, $base64, $packageid);
        $header_statement->execute();

        $delete_detail = "DELETE FROM packagedetail WHERE PackageID = ?";
        $delete_statement = $conn->prepare($delete_detail);
        $delete_statement->bind_param("i", $packageid);
        $delete_statement->execute();

        foreach ($menus as $menu) {
            $menu = (array)$menu;
            $query_detail = "INSERT INTO packagedetail VALUES (?, ?, ?)";
            $detail_statement = $conn->prepare($query_detail);
            $detail_statement->bind_param("iii", $packageid, $menu['foodid'], $menu['quantity']);
            $detail_statement->execute();
        }
    }

    $query = "SELECT * FROM packageheader WHERE PackageID = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("i", $packageid);
    $prepared_statement->execute();
    $datas = $prepared_statement->get_result();

    $packages = array();
    while($row = $datas->fetch_assoc()) {
        $query_detail = "SELECT f.FoodID, FoodName, FoodPrice, Quantity FROM packagedetail AS pd JOIN food AS f ON pd.FoodID = f.FoodID WHERE PackageID = ?";
        $detail_statement = $conn->prepare($query_detail);
        $detail_statement->bind_param("i", $row['PackageID']);
        $detail_statement->execute();
        $details = $detail_statement->get_result();
        $detail_list = array();
        while($detail_row = $details->fetch_assoc()) {
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

    echo json_encode($packages);
    die();
}

?>