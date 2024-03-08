<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';
    require $_SERVER['DOCUMENT_ROOT'].'/utils/validator.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['manage_token']) && $_SESSION['manage_token'] == $_POST['token']) {
        $id = htmlspecialchars($_POST['foodid']);
        $name = htmlspecialchars($_POST['update_foodname']);
        $price = htmlspecialchars($_POST['update_price']);
        $category = htmlspecialchars((int)$_POST['update_category']);
        $description = htmlspecialchars($_POST['update_description']);

        $return_data = array("error"=>"");

        if (check_empty($name) || check_empty($price) || $category == 0 || check_empty($description)) {
            $return_data['error'] = "All fields must be filled!";
            echo json_encode($return_data);
            die();
        } else if ((int)$price <= 0) {
            $return_data['error'] = "Price must be more than 0!";
            echo json_encode($return_data);
            die();
        } else if (check_length($description, 0, 200)) {
            $return_data['error'] = "Description cannot contains more than 200 characters!";
            echo json_encode($return_data);
            die();
        } else {
            if ($_FILES['update_image']['error'] > 0) {
                $query = "UPDATE Food SET FoodName = ?, FoodPrice = ?, FoodCategoryID = ?, FoodDescription = ? WHERE FoodID = ?";
                $price = (int)$price;

                $prepared_statement = $conn->prepare($query);
                $prepared_statement->bind_param("siisi", $name, $price, $category, $description, $id);
            } else {
                $query = "UPDATE Food SET FoodName = ?, FoodPrice = ?, FoodCategoryID = ?, FoodDescription = ?, FoodImage = ? WHERE FoodID = ?";
                $type = $_FILES['update_image']['type'];

                if ($type != "image/jpg" && $type != "image/png" && $type != "image/jpeg") {
                    $return_data = "Image must be in jpg/png/jpeg format!";
                    return json_encode($return_data);
                    die();
                }

                $image = file_get_contents($_FILES['update_image']['tmp_name']);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($image);

                $price = (int)$price;

                $prepared_statement = $conn->prepare($query);
                $prepared_statement->bind_param("siissi", $name, $price, $category, $description, $base64, $id);
            }

            $prepared_statement->execute();
        }

        $query = "SELECT * FROM Food WHERE FoodID = ?";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("i", $id);
        $prepared_statement->execute();
        $result = $prepared_statement->get_result();

        echo json_encode($result->fetch_assoc());
        die();

    }
    
    header("Location: " . $_SERVER["HTTP_REFERER"]);
