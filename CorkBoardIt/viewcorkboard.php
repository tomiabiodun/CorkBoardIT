<?php
session_start();

// Get the board_id from the URL and make sure it's an integer
$board_id = intval($_GET['board_id']); // Ensure integer input for security

$servername = "localhost";
$username = "root";
$db_password = "abcd1234";
$dbname = "CorkBoardIt";
$conn = new mysqli($servername, $username, $db_password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT visibility FROM corkboard WHERE board_id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $board_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $visibility = $row['visibility'];

    if($visibility === "public"){
        $_SESSION['authenticated_' . $board_id] = true;
    }

    // Verify if the user password matches the stored password
}

$stmt->close();
$conn->close();

// Check if the form is submitted
if (isset($_POST['private'])) {
    // Get the password entered by the user
    $user_password = $_POST['password'];

    // Connect to MySQL database
    $servername = "localhost";
    $username = "root";
    $db_password = "abcd1234";
    $dbname = "CorkBoardIt";
    $conn = new mysqli($servername, $username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the stored password for the specific board
    $sql = "SELECT board_password FROM corkboard WHERE board_id = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $board_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['board_password'];

        // Verify if the user password matches the stored password
        if ($user_password === $stored_password) {
            // Authenticate the user for this specific board
            $_SESSION['authenticated_' . $board_id] = true;
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "Board not found.";
    }

    $stmt->close();
    $conn->close();
}

// Check if user is authenticated for this specific board, and display content if they are
if (isset($_SESSION['authenticated_' . $board_id]) && $_SESSION['authenticated_' . $board_id] === true) {

    // Add your actual board content here
?>

<?php
// Start the session
//session_start();


$user_id = $_SESSION['user_id'] ?? null; // Replace with actual logic

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    die("No user is logged in");
}

// Database connection variables
$host = 'localhost';       // Your database host
$db = 'CorkBoardIt';     // Your database name
$user = 'root';    // Your database user
$pass = 'abcd1234';// Your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = '';

/*
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['first_name'];
$lastname = $_SESSION['last_name'];
*/

if (isset($_GET['board_id'])) {
    $board_id = intval($_GET['board_id']); // Make sure it's an integer for security

     // Query to fetch corkboard details
     $sql = "SELECT a.user_id, a.board_name, a.board_category, b.first_name, b.last_name
            FROM corkboard a
            LEFT JOIN users b
            ON a.user_id = b.user_id 
            WHERE board_id = ?;
";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $board_id);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows > 0) {
        // Display corkboard details
        $row = $result->fetch_assoc();
        $title = $row['board_name'];
        $category = $row['board_category'];
        $userid = $row['user_id'];
        $firstname = $row["first_name"];
        $lastname = $row["last_name"];
        //$last_updated = $row["datentime"];
    } else {
        echo "Corkboard not found.";
    }

    $stmt->close();
} else {
    echo "No board_id provided.";
}



// Prepare the SQL query to prevent SQL injection
$sql = $conn->prepare("SELECT datentime 
            FROM pushpin
            WHERE board_id = ?
            ORDER BY datentime DESC
            LIMIT 1;");
$sql->bind_param("i", $board_id); // 'i' means the user_id is an integer

// Execute the query
$sql->execute();
$result = $sql->get_result();

// Check if any result is returned
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $last_updated = $row["datentime"];
        //echo $last_updated;
        // Convert to timestamp
        $timestamp = strtotime($last_updated);

        // Format the date
        $formatted_date = date('F j, Y \a\t g:iA', $timestamp);
               
    }
} else {
    $formatted_date = "No pushpin present"; 
}
// Close the statement and connection
$sql->close();








$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Corkboard View</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                margin: 20px;
            }
            .corkboard {
                width: 300px;
                border: 1px solid #ccc;
                background-color: #fff;
                padding: 15px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                margin-left: auto;
                margin-right: auto;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .header .title {
                font-size: 18px;
                font-weight: bold;
            }
            .header .category {
                font-size: 12px;
                color: #0073e6;
                text-decoration: none;
            }
            .follow-btn {
                background-color: #e0e0e0;
                border: none;
                padding: 5px 10px;
                font-size: 12px;
                cursor: pointer;
            }
            .last-updated {
                font-size: 12px;
                color: #888;
                margin-bottom: 10px;
            }
            .add-pushpin {
                display: block;
                margin: 10px 0;
                padding: 5px 10px;
                background-color: #0073e6;
                color: white;
                text-align: center;
                border-radius: 4px;
                font-size: 12px;
                text-decoration: none;
            }
            .image-gallery {
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-gap: 10px;
            }
            .image-gallery img {
                width: 100%;
                border-radius: 4px;
            }
            .watch-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 15px;
            }
            .watch-section .watchers {
                font-size: 12px;
            }
            .watch-section .watch-btn {
                background-color: #f0f0f0;
                border: 1px solid #ccc;
                padding: 5px 10px;
                font-size: 12px;
                cursor: pointer;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <a href= "dashboard.php"><img src = "assets/image/logo2.png" style = "height: 50px; margin-left: 50%; margin-right: auto;"></a>

        <div class="corkboard">
            <div class="header">
                <div>
                    <div class="title"><?php echo $firstname . ' ' . $lastname; ?></div>
                    <div class="subtitle"><?php echo $title; ?></div>
                </div>
                <div>
                    <form method = "POST">
                        <?php

                        // Database connection variables
                        $host = 'localhost';       // Your database host
                        $db = 'CorkBoardIt';     // Your database name
                        $user = 'root';    // Your database user
                        $pass = 'abcd1234';// Your database password

                        // Create connection
                        $conn = new mysqli($host, $user, $pass, $db);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Initialize error message
                        $error = '';

                        $follower_id = $_SESSION['user_id']; // Logged-in user ID (the follower)
                        $followed_id = $userid; // The ID of the user being followed





                        $checkQuery = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
                        $stmt = $conn->prepare($checkQuery);
                        $stmt->bind_param("ii", $follower_id, $followed_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
        
                        if ($result->num_rows == 0) {
                            $follow_status = "Follow";

                        }
                        else{
                            $follow_status = "Unfollow";

                        }
                        $stmt->close();





                        if (isset($_POST['follow'])) {

                            // Assume $follower_id and $followee_id are passed through POST request or session





                            // Prevent users from following themselves
                            if ($follower_id != $followed_id) {
                                // Check if the follow relationship already exists
                                $checkQuery = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
                                $stmt = $conn->prepare($checkQuery);
                                $stmt->bind_param("ii", $follower_id, $followed_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows == 0) {
                                    // If the follow relationship does not exist, insert it
                                    $query = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("ii", $follower_id, $followed_id);
                                    if ($stmt->execute()) {
                                        $follow_status = 'Unfollow';
                                        //echo "Successfully followed user.";
                                    } else {
                                        echo "Error following user.";
                                    }
                                } else {
                                    $query = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("ii", $follower_id, $followed_id);
                                    if ($stmt->execute()) {
                                        $follow_status = "Follow";
                                        //echo "Successfully unfollowed user.";
                                    } else {
                                        echo "Error unfollowing user.";
                                    }

                                    //echo "You are already following this user.";
                                }
                            } else {
                                //echo "You cannot follow yourself.";
                            }
                        }


                        echo "<button class='follow-btn' type = 'submit' name = 'follow'>$follow_status</button>";
                        $conn->close();

                        ?>
                    </form>
                    <a href="" class="category"><?php echo $category; ?></a>
                </div>
            </div>

            <div class="last-updated">Last Updated <?php echo $formatted_date;?><!--January 16, 2012 at 11:57AM--></div>

            <a href="addpushpin.php?board_id=<?php echo $board_id;?>" class="add-pushpin">Add PushPin</a>

            <div class="image-gallery">
            <?php
            // Database credentials
            $servername = "localhost";
            $username = "root";
            $password = "abcd1234"; // Your database password
            $dbname = "CorkBoardIt"; // Your database name

            // Get the board_id from a parameter (e.g., URL or form submission)

            //$board_id = isset($_GET['board_id']) ? (int)$_GET['board_id'] : 0;

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare and execute the SQL query
            $sql = "SELECT url, pushpin_id FROM pushpin WHERE board_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $board_id); // Bind the board_id parameter (integer)
            $stmt->execute();
            $result = $stmt->get_result(); // Get the result set

            // Check if any rows were returned
            if ($result->num_rows > 0) {
                // Output each image
                while($row = $result->fetch_assoc()) {
                    $image_url = htmlspecialchars($row['url']); // Sanitize the URL
                    $pushpin_id = $row['pushpin_id'];
                    echo "<a href = 'viewpushpin.php?pushpin_id=$pushpin_id'><div><img src='$image_url' alt='Pushpin Image' style='max-width: 300px; height: auto;'></div></a>";
                    //echo $image_url;
                }
            } else {
                //echo "No images found for this board.";
            }

            // Close the connection
            $stmt->close();
            $conn->close();
            ?>
            </div>













            <div class="watch-section">
                <form method = "post">
                    <?php
                        // Database credentials
                        $servername = "localhost";
                        $username = "root";
                        $password = "abcd1234"; // Your database password
                        $dbname = "CorkBoardIt"; // Your database name
        
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);
    
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }







                        $checkQuery = "SELECT * FROM watchers WHERE board_id = ? AND user_id = ?";
                        $stmt = $conn->prepare($checkQuery);
                        $stmt->bind_param("ii", $board_id, $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
        
                        if ($result->num_rows == 0) {
                            $watch_status = "watch";

                        }
                        else{
                            $watch_status = "Remove from watchlist";

                        }
                        $stmt->close();





                        if (isset($_POST['watchers']) && $visibility === "public") {

                            // Assume $follower_id and $followee_id are passed through POST request or session





                            // Prevent users from following themselves
                            if ($user_id != $userid) {
                                // Check if the follow relationship already exists
                                $checkQuery = "SELECT * FROM watchers WHERE board_id = ? AND user_id = ?";
                                $stmt = $conn->prepare($checkQuery);
                                $stmt->bind_param("ii", $board_id, $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows == 0) {
                                    // If the follow relationship does not exist, insert it
                                    $query = "INSERT INTO watchers (board_id, user_id) VALUES (?, ?)";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("ii", $board_id, $user_id);
                                    if ($stmt->execute()) {
                                        $watch_status = 'remove from watchlist';
                                        //echo "Successfully followed user.";
                                    } else {
                                        echo "Error watching user.";
                                    }
                                } else {
                                    $query = "DELETE FROM watchers WHERE board_id = ? AND user_id = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("ii", $board_id, $user_id);
                                    if ($stmt->execute()) {
                                        $watch_status = "watch";
                                        //echo "Successfully unfollowed user.";
                                    } else {
                                        echo "Error unfollowing user.";
                                    }

                                    //echo "You are already following this user.";
                                }
                            } else {
                                //echo "You cannot follow yourself.";
                            }
                        }


                        // Prepare the SQL query to prevent SQL injection
                        $sql = $conn->prepare("SELECT COUNT(*) AS count_watchers FROM watchers WHERE board_id = ?;");
                        $sql->bind_param("i", $board_id); // 'i' means the user_id is an integer

                        // Execute the query
                        $sql->execute();
                        $result = $sql->get_result();

                        // Check if any result is returned
                        $row = $result->fetch_assoc();

                        $count_watchers = $row['count_watchers'];





                        echo "<div class='watchers'>This CorkBoard has $count_watchers watchers.</div>";    
                
                
                    $conn->close();

                    echo "<button class='watch-btn' type = 'submit' name = 'watchers' >$watch_status</button>"
                ?>
                

            </div>
        </div>
        <script src="assets/script.js"></script>
    </body>
</html>










<?php



} else {
    ?>
    <!-- Password form -->
    <html>
    <head>
        <title>Password Protected</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .container {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                max-width: 400px;
                text-align: center;
            }

            h2 {
                color: #333;
            }

            form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            label {
                font-size: 16px;
                color: #555;
            }

            input[type="password"] {
                padding: 10px;
                font-size: 16px;
                border: 1px solid #ddd;
                border-radius: 4px;
                width: 100%;
            }

            button {
                padding: 10px;
                font-size: 16px;
                color: white;
                background-color: #5a67d8;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            button:hover {
                background-color: #4c51bf;
            }

            .error {
                color: #e53e3e;
                font-size: 14px;
                margin-bottom: 10px;
            }

            .welcome-message {
                text-align: center;
                font-size: 20px;
                color: #2d3748;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Password Protected</h2>
            <?php if (isset($error_message)) { ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php } ?>
            <form method="POST">
                <label for="password">Please enter the password to view this CorkBoard:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="private">Submit</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
