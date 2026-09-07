<?php
// Start the session if you are going to use $_SESSION for user_id
session_start();

// Assuming you have the user_id already set in $_SESSION after login
$user_id = $_SESSION['user_id'] ?? null; // Replace with actual logic

// MySQL Database Connection
$servername = "localhost";
$username = "root";
$password = "abcd1234";
$dbname = "CorkBoardIt";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




if (isset($_GET['board_id'])) {

    $board_id = intval($_GET['board_id']); // Make sure it's an integer for security



    // Checks if user is the owner of corkboard

    // Prepare the SQL query to prevent SQL injection
    $sql = $conn->prepare("SELECT user_id FROM corkboard WHERE board_id = ?;");
    $sql->bind_param("i", $board_id); // 'i' means the board_id is an integer

    // Execute the query
    $sql->execute();
    $result = $sql->get_result();

    // Check if any result is returned
    if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
    $userid = $row["user_id"];

    if ( $user_id != $userid){
        header("Location: viewcorkboard.php?board_id=" . $board_id);
            exit();
    }

    }
    } else {
    //echo "0 results";
    }
    // Close the statement and connection
    $sql->close();







    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Collect form data
        $url = $_POST['url'];
        $description = $_POST['description'];
        $tags = $_POST['tags'];
    
        // Validate form inputs
        if (empty($url) || empty($description)) {
            echo "Please fill in all required fields.";
        } else {
            // Prepare the SQL query to insert the data
            $stmt = $conn->prepare("INSERT pushpin (board_id, url, `description`, tags, datentime) VALUES(?,?,?,?, NOW());");
            $stmt->bind_param("isss", $board_id, $url, $description, $tags);
    
            // Execute the query
            if ($stmt->execute()) {
                echo "<p class='success'>Board created successfully!</p>";

                //$board_id = $conn->insert_id;
                
                header("Location: viewcorkboard.php?board_id=" . $board_id);
                exit();
    
            }
            $stmt->close();
        }
        
    }    
}
else {
    echo "No board_id provided.";
}





if (isset($_GET['board_id'])) {
    $board_id = intval($_GET['board_id']); // Make sure it's an integer for security

     // Query to fetch corkboard details
     $sql = "SELECT board_name
            FROM corkboard 
            WHERE board_id = ?;";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $board_id);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows > 0) {
        // Display corkboard details
        $row = $result->fetch_assoc();
        $title = $row['board_name'];

    } else {
        echo "Corkboard not found.";
    }

    $stmt->close();
} else {
    echo "No board_id provided.";
}





$conn->close();
?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add PushPin Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .form-container {
            width: 400px;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-left: auto;
            margin-right: auto;
        }
        .form-container h2 {
            margin-top: 0;
            color: #333;
        }
        .form-container label {
            font-weight: bold;
            margin-bottom: 5px;
            display: inline-block;
        }
        .form-container input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container .note {
            font-size: 12px;
            color: #666;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .form-container input[type="submit"] {
            background-color: #0073e6;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-container input[type="submit"]:hover {
            background-color: #005bb5;
        }
        .pushpin-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add PushPin to <?php echo $title;?></h2>

    <form method="POST">
        <label for="url">URL</label>
        <input type="text" id="url" name="url" required>

        <label for="description">Description</label>
        <input type="text" id="description" name="description" required >

        <label for="tags">Tags</label>
        <input type="text" id="tags" name="tags">

        <!--<div class="note">Tags are separated by commas and parsed by the application. Tags are optional.</div>-->

        <input type="submit" value="Add">
    </form>

    <img src="assets/image/pushpin-icon.png" alt="PushPin Icon" class="pushpin-icon">
</div>

</body>
</html>
