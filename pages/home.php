<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/controller/connection.php';

if (!isset($_SESSION['logged_user'])) {
    header("Location: /pages/auth/login.php");
}

$query = "SELECT * FROM User WHERE Username = ?";
$prepared_statement = $conn->prepare($query);
$prepared_statement->bind_param("s", $_SESSION['logged_user']);
$prepared_statement->execute();
$result = $prepared_statement->get_result();

$data = $result->fetch_assoc();

$userid = (int)$data['UserID'];
$notif_query = "SELECT * FROM Notification WHERE ReceiverID = ? AND NotificationStatus = 'unseen'";
$prepared_statement = $conn->prepare($notif_query);
$prepared_statement->bind_param("i", $userid);
$prepared_statement->execute();
$notif_data = $prepared_statement->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | LAvFood</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex bg-amber-50">
        <div class="h-screen w-1/4">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/layout/sidebar.php' ?>
        </div>
        <div class="flex-auto w-3/4 px-4 py-4">
            <div class="flex">
                <?php if ($data['UserGender'] == "male") { ?>
                    <img class="w-20 h-20 rounded-full lg:w-24 lg:h-24 mr-4" src="https://avataaars.io/?avatarStyle=Transparent&amp;topType=ShortHairShortFlat&amp;accessoriesType=Blank&amp;hairColor=Black&amp;facialHairType=Blank&amp;clotheType=CollarSweater&amp;clotheColor=Black&amp;eyeType=Default&amp;eyebrowType=RaisedExcitedNatural&amp;mouthType=Twinkle&amp;skinColor=Light">
                <?php } else { ?>
                    <img class="w-20 h-20 rounded-full lg:w-24 lg:h-24 mr-4" src="https://avataaars.io/?avatarStyle=Transparent&amp;topType=LongHairStraight2&amp;accessoriesType=Blank&amp;hairColor=Black&amp;facialHairType=Blank&amp;clotheType=GraphicShirt&amp;clotheColor=Pink&amp;graphicType=Bear&amp;eyeType=Default&amp;eyebrowType=DefaultNatural&amp;mouthType=Twinkle&amp;skinColor=Light">
                <?php } ?>
                <div>
                    <p class="text-3xl font-bold text-stone-800">Welcome, <?= $_SESSION['logged_user'] ?></p>
                    <div class="flex mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-stone-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="ml-2"><?= $data['UserEmail'] ?></p>
                    </div>
                    <div class="flex mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-stone-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <p class="ml-2">+62 <?= $data['UserPhoneNumber'] ?></p>
                    </div>
                </div>
            </div>
            <div class="mt-8 w-full">
                <?php while ($row = $notif_data->fetch_assoc()) { ?>
                    <div class="w-full bg-amber-100/75 drop-shadow flex justify-between rounded-md px-3 py-2 mt-4">
                        <p class="w-3/4 break-all"><?= $row['NotificationMessage'] ?></p>
                        <form action="/controller/see-notif-controller.php" method="POST">
                            <input type="hidden" value="<?= $row['NotificationID'] ?>" id="notifid" name="notifid">
                            <button type="submit" id="see-notif" name="see-notif">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-stone-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>