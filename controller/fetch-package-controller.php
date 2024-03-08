<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searched = isset($_POST['packagename']) ? htmlspecialchars($_POST['packagename']) : ""; 

    if ($data['UserRoleID'] == 4 || $data['UserRoleID'] == 5) {
        $query = "SELECT * FROM packageheader WHERE PackageName LIKE ?";
        $parameter = "%".$searched."%";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("s", $parameter);
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
    } else {
        $query = "SELECT * FROM packageheader WHERE PackageName LIKE ? AND CreatedBy = ?";
        $parameter = "%".$searched."%";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("si", $parameter, $data['UserID']);
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
}

?>