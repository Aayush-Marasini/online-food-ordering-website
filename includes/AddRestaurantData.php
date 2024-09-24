<?php
session_start();

include 'dbh.inc.php';

if (isset($_POST['addrestaurant'])) {
    $ownername = $_SESSION['user_name'];

    $name = mysqli_real_escape_string($conn, $_POST['restaurantname']);
    $address = mysqli_real_escape_string($conn, $_POST['restaurantaddress']);
    $cuisine = implode(',', $_POST['cuisine']);
    $email = mysqli_real_escape_string($conn, $_POST['restaurantemail']);
    $open = mysqli_real_escape_string($conn, $_POST['open']);
    $close = mysqli_real_escape_string($conn, $_POST['close']);

    // Sanitize restaurant name for use as table name
    $restname = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));

    if (isset($_FILES['image'])) {
        $imagename = $_FILES['image']['name'];
        $destinationFile = 'images/' . $imagename;
        move_uploaded_file($_FILES['image']['tmp_name'], $destinationFile);
    }

    $sqlInsert = "INSERT INTO restaurants (rest_ownername, rest_name, rest_address, rest_email, rest_open, rest_close, rest_cuisine, rest_rating, rest_image) 
                  VALUES ('$ownername', '$name', '$address', '$email', '$open', '$close', '$cuisine', '0', '$destinationFile')";
    if (mysqli_query($conn, $sqlInsert)) {
        $haverestUpdate = "UPDATE users SET user_haverest='1' WHERE user_name = '$ownername'";
        mysqli_query($conn, $haverestUpdate);

        $addRest = "CREATE TABLE `$restname` (
                        dish_id int(100) AUTO_INCREMENT PRIMARY KEY,
                        cuisine varchar(50),
                        type varchar(50),
                        cost int(100)
                    )";
        if (!mysqli_query($conn, $addRest)) {
            echo mysqli_error($conn);
        }
        header("Location: ../userLoggedIn.php?RestaurantAdded");
        exit();
    } else {
        echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
    }
}
?>
