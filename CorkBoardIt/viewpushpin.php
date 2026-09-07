<?php
// Start the session
session_start();

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

if (isset($_GET['pushpin_id'])) {
    $pushpin_id = intval($_GET['pushpin_id']); // make int for security

    //query
    $stmt = $conn->prepare("SELECT a.board_id, a.url, a.description, a.tags, a.datentime, b.board_name, b.user_id, c.first_name, c.last_name  FROM pushpin a LEFT JOIN corkboard b ON a.board_id = b.board_id LEFT JOIN users c ON b.user_id = c.user_id WHERE pushpin_id = ?;");
    $stmt->bind_param("i", $pushpin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        // Display pushpin details
        $row = $result->fetch_assoc();
        //storing the pushpin information
        $board_id = $row['board_id'];
        $url = $row['url'];
        $description = $row['description'];
        $tags = $row['tags'];
        $datentime = $row['datentime'];
        $board_name = $row['board_name'];
        $userid = $row['user_id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $last_updated = $row["datentime"];
        //echo $last_updated;
        // Convert to timestamp
        $timestamp = strtotime($last_updated);

        // Format the date
        $formatted_date = date('F j, Y \a\t g:iA', $timestamp);

    }
    else{
        echo "pushpin does not exist";
    }
}
$stmt->close();

$follower_id = $_SESSION['user_id']; // Logged-in user ID (the follower)
$followed_id = $userid; // The ID of the user being followed

//checks the following status
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

// follow button action

if (isset($_POST['follow'])) {

    // The actionss and doings

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
            }
            else {
                echo "Error following user.";
            }
        }
        else {
            $query = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $follower_id, $followed_id);
            if ($stmt->execute()) {
                $follow_status = "Follow";
                //Successfully unfollowed user;
            } else {
                echo "Error unfollowing user.";
            }

            //echo "You are already following this user.";
        }
        $stmt->close();

        }
        else {
            //echo "You cannot follow yourself.";
        }
        

}


//Like feature

//checks the like status
$checkQuery = "SELECT * FROM likes WHERE user_id = ? AND pushpin_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $user_id, $pushpin_id);
$stmt->execute();
$result = $stmt->get_result();
        
if ($result->num_rows == 0) {
    $like_status = "Like";

}
else{
    $like_status = "Unlike";
}
$stmt->close();

// follow button action

if (isset($_POST['like'])) {

    // The actionss and doings

    // Prevent users from following themselves
    if ($user_id != $userid) {
        // Check if the follow relationship already exists
        $checkQuery = "SELECT * FROM likes WHERE user_id = ? AND pushpin_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $user_id, $pushpin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // If the follow relationship does not exist, insert it
            $query = "INSERT INTO likes (user_id, pushpin_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $pushpin_id);
            if ($stmt->execute()) {
                $like_status = 'Unlike';
                //Successfully liked pushpin
            } else {
                echo "Error liking pushpin.";
            }
        }
        else {
            $query = "DELETE FROM likes WHERE user_id = ? AND pushpin_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $pushpin_id);
            if ($stmt->execute()) {
                $like_status = "Like";
                //Successfully unliked user;
            }
            else {
                echo "Error liking pushpin user.";
            }

            //echo "You are already following this user.";
        }
        $stmt->close();
    }
    else {
        //echo "You cannot like your pushpin.";
    }
        
}




//input Comment to datase kini

if(isset($_POST['comment'])){
    $comment_text = $_POST['comment_text'];

    // Validate form inputs
    if (empty($comment_text)) {
        //echo "Please fill in all required fields.";
    }
    else {
        // Prepare the SQL query to insert the data
        $stmt = $conn->prepare("INSERT comments (user_id, pushpin_id, comment_text) VALUES(?, ?, ?);");
        $stmt->bind_param("iis", $user_id, $pushpin_id, $comment_text);
        
        // Execute the query
        $stmt->execute();

        // $stmt closed                
        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pin Details</title>
    <!--<link rel="stylesheet" href="styles.css">-->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }

        .pin-container {
            width: 400px;
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-left: auto;
            margin-right:auto;
        }

        .pin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .follow-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .pinned-date {
            font-size: 12px;
            color: #777;
        }

        .pin-image {
            margin-top: 10px;
            position: relative;
        }

        .pin-image img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .image-source {
            font-size: 12px;
            color: #777;
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2px 5px;
            border-radius: 3px;
        }

        .pin-description {
            margin-top: 15px;
    }

        .tags {
            font-size: 12px;
            color: #777;
        }

        .liked-by {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .like-btn {
            background-color: #ff5722;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .comments-section {
            margin-top: 20px;
        }

        .comment {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .add-comment {
            margin-top: 10px;
        }

        .add-comment textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none;
        }

        .post-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            margin-top: 5px;
            cursor: pointer;
            border-radius: 3px;
        }

        
    </style>
</head>
<body>
<a href= "dashboard.php"><img src = "assets/image/logo2.png" style = "height: 50px; margin-left: 50%; margin-right: auto;"></a>
    <div class="pin-container">
        <div class="pin-header">
            <h2><?php echo "$first_name $last_name";?></h2>
            <form method = "post">
                <button class="follow-btn" name = "follow"><?php echo $follow_status;?></button>
            </form>
        </div>
        <p class="pinned-date">Pinned <?php echo "$formatted_date on <a href='viewcorkboard.php?board_id=$board_id'>$board_name"; ?></a></p>
        <div class="pin-image">
            
            <img src="<?php echo $url;?>" alt="Swimming pool image">
            
            <!--<span class="image-source">from poolswimmings.com</span>-->
        </div>
        <div class="pin-description">
            <p><?php echo $description;?></p>
            <p><strong>Tags:</strong> <span class="tags"><?php echo $tags;?></span></p>
        </div>
        <div class="liked-by">
            <p><strong><img src="https://thinglabs.io/wp-content/uploads/image-43.png" style = "width: 50px;"/><!--Liked by:--></strong> 
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
            // Prepare and execute the SQL query
            $sql = "SELECT b.first_name, b.last_name, COUNT(a.user_id) AS number_of_likes FROM likes a LEFT JOIN users b ON a.user_id = b.user_id WHERE pushpin_id = ? GROUP BY b.first_name, b.last_name;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pushpin_id); // Bind the board_id parameter (integer)
            $stmt->execute();
            $result = $stmt->get_result(); // Get the result set
            
            // Check if any rows were returned
            if ($result->num_rows > 0) {
                $ppl_count = 0;
                
                // Output each likes
                while($row = $result->fetch_assoc()) {

                    $firstname = $row['first_name'];
                    $lastname = $row['last_name'];
                    
                    $ppl_count = $ppl_count + 1;

                    if($ppl_count == 1){
                        //$number_of_likes = $row['number_of_likes'];
                        echo "$firstname $lastname, ";
                    }
                    elseif($ppl_count == 2){
                        echo "$firstname $lastname ";
                    }
                    else{
                        if($result->num_rows > 3){
                            $others = $result->num_rows - 2;
                            echo "and $others others";
                            break;
                        }
                
                        else{
                            echo "and $firstname $lastname";
                        }
                        
                    }
                }
            }
            else {
                //IDEK fr
            }
            
            // Close the connection
            $stmt->close();
            $conn->close();
            ?>
            </p>
            <form method = "post">
                <button class="like-btn" name = "like"><?php echo $like_status;?>!</button>
            </form>
        </div>
        <div class="comments-section">
            <h3>Comments</h3>
            
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
                // Prepare and execute the SQL query
                $sql = "SELECT b.first_name, b.last_name, a.comment_text FROM comments a LEFT JOIN users b ON a.user_id = b.user_id WHERE pushpin_id = ?;";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $pushpin_id); // Bind the board_id parameter (integer)
                $stmt->execute();
                $result = $stmt->get_result(); // Get the result set
            
                // Check if any rows were returned
                if ($result->num_rows > 0) {                
                    // Output each likes
                    while($row = $result->fetch_assoc()) {

                        $fname = $row['first_name'];
                        $lname = $row['last_name'];
                        $commenttext = $row['comment_text'];

                        echo "<div class='comment'><p><strong>$fname $lname:</strong> $commenttext</p></div>";
                    }
                }
                else {
                //IDEK fr
                }
            
                // Close the connection
                $stmt->close();
                $conn->close();
                ?>
            
            <!--<div class="comment">
                <p><strong>Sybil Crawley:</strong> Why not?!</p>
            </div>-->
            <div class="add-comment">
                <form method="post">
                    <textarea placeholder="Enter Comment" name="comment_text"></textarea>
                    <button type="submit" class="post-btn" name="comment">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
