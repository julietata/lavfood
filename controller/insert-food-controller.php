<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    $query = "SELECT * FROM User WHERE Username = ?";
    $prepared_statement = $conn->prepare($query);
    $prepared_statement->bind_param("s", $_SESSION['logged_user']);
    $prepared_statement->execute();
    $result = $prepared_statement->get_result();
    $data = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert_food']) && isset($_SESSION['manage_token']) && $_SESSION['manage_token'] == $_POST['token']) {
        $name = htmlspecialchars($_POST['foodname']);
        $price = htmlspecialchars($_POST['price']);
        $category = htmlspecialchars((int)$_POST['category']);
        $description = htmlspecialchars($_POST['description']);

        if (check_empty($name) || check_empty($price) || $category == 0 || check_empty($description)) {
            $_SESSION['insert_food_error'] = "All fields must be filled!";
        } else if ((int)$price <= 0) {
            $_SESSION['insert_food_error'] = "Price must be more than 0!";
        } else if (!isset($_FILES) || $_FILES['image']['error'] > 0) {
            $_SESSION['insert_food_error'] = "Insert Food Image!";
        } else if (check_length($description, 0, 200)) {
            $_SESSION['insert_food_error'] = "Description cannot contains more than 200 characters!";
        } else {
            $query = "INSERT INTO food VALUES (null, ?, ?, ?, ?, ?, ?)";

            $type = $_FILES['image']['type'];

            if ($type != "image/jpg" && $type != "image/png" && $type != "image/jpeg") {
                $_SESSION['insert_food_error'] = "Image must be in jpg/png/jpeg format!";
            } else {
                $image = file_get_contents($_FILES['image']['tmp_name']);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image);

                $price = (int)$price;
                $userid = (int)$data['UserID'];

                $prepared_statement = $conn->prepare($query);
                $prepared_statement->bind_param("ssiiis", $base64, $name, $userid, $price, $category, $description);
                $prepared_statement->execute();
            }
        }
    } else {
        $_SESSION['insert_food_error'] = "Invalid Request";
    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);
?>