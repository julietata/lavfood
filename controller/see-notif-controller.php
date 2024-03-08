<?php

    require $_SERVER['DOCUMENT_ROOT'].'/controller/connection.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['see-notif'])) {
        $notifid = $_POST['notifid'];
        $query = "UPDATE Notification SET NotificationStatus = 'seen' WHERE NotificationID = ?";
        $prepared_statement = $conn->prepare($query);
        $prepared_statement->bind_param("i", $notifid);
        $prepared_statement->execute();
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);

?>