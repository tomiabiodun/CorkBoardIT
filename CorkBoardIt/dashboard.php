<?php
// Start the session
session_start();

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


$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['first_name'];
$lastname = $_SESSION['last_name'];



/*
// Prepare the SQL query to prevent SQL injection
$sql = $conn->prepare("SELECT user_id, first_name, last_name FROM users WHERE user_id = ?");
$sql->bind_param("i", $user_id); // 'i' means the user_id is an integer

// Execute the query
$sql->execute();
$result = $sql->get_result();

// Check if any result is returned
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $firstname = $row["first_name"];
        $lastname = $row["last_name"];
       // echo "ID: " . $row["user_id"] . " - Name: " . $row["first_name"] . $row["last_name"] . "<br>";
    }
} else {
    //echo "0 results";
}





// Close the statement and connection

$sql->close();

*/
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkBoardIt</title>
    <link href="assets/style.css" rel="stylesheet" >
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div style=  "margin-top: 50px; ">
    <section style = "background-color: #d5dbd6; border-radius: 7px; width: 50%; margin-left: auto; margin-right: auto; padding-bottom: 5px; ">
        <p style= "margin-left: 5px;">CorkBoardIt Home Page</p>
        <div style = "background-color: White; margin: 5px;">
            </br>
            <div style = "display: flex;">
                <div style="color:#8cc295; margin-left:7px; ">
                    <h5>Home Page for</h5>
                    <h4>
                    <?php
                     echo $firstname;
                     echo " ";
                     echo $lastname;
                    ?>
                    </h4>
                </div>
                <div>
                    <img src="assets/image/logo2.png" alt="Logo" style = "width: 60%; margin-left: 40%;">
                </div>
            </div>
            <div class="hr">
                <hr>
            </div>
            </br>
            <!--RECENT CORKBOARD UPDATES BEGINS-->
            <div>
                <div  style="display:flex">
                    <p  class="heading" style="margin-left: 20px">Recent CorkBoard Updates</p>
                    <button style="margin-left: 25%; border-radius: 5px;padding-left: 8px; padding-right: 8px; height: 70%;">Popular Tags</button>
                </div>
                <!--DISPLAY RECENT CORKBOARDS-->
                <div>

                    <div class="container mt-5">
                        <div class="corkboard-section">
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
                            $sql = "SELECT a.board_id, a.board_name, a.visibility, b.first_name, b.last_name, c.datentime FROM corkboard a LEFT JOIN users b ON a.user_id = b.user_id LEFT JOIN pushpin c ON a.board_id = c.board_id LEFT JOIN follows d ON d.followed_id = a.user_id LEFT JOIN watchers e ON e.board_id = a.board_id WHERE d.follower_id = ? OR e.user_id = ? ORDER BY datentime DESC LIMIT 4;";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $user_id, $user_id); // Bind the user_id parameter (integer)
                            $stmt->execute();
                            $result = $stmt->get_result(); // Get the result set

                            // Check if any rows were returned
                            if ($result->num_rows > 0) {
                                // Output each image
                                while($row = $result->fetch_assoc()) {

                                    $boardid = $row['board_id'];
                                    $boardname = $row['board_name'];
                                    $firstname = $row['first_name'];
                                    $lastname = $row['last_name'];
                                    $last_updated = $row["datentime"];
                                    $timestamp = strtotime($last_updated);                            
                                    // Format the date
                                    $formatted_date = date('F j, Y \a\t g:iA', $timestamp);
                                    $vis = $row['visibility'];




                                    echo "<div class='corkboard-item'>
                                    <a href='viewcorkboard.php?board_id=$boardid' class='corkboard-title'>$boardname</a>";

                                    if($vis === "private"){ echo "<span class='private'>(private)</span>";}

                                    echo "<p class='updated-by'>Updated by $firstname $lastname on <span class='date'>$formatted_date</p>
                                    </div>";
                                }
                            }
                            else {
                                echo "<H1 style = 'text-align: center;'>“You have no Recent CorkBoards.</H1>";
                            }

                            // Close the connection


                        

                            $stmt->close();
                            $conn->close();

                            ?>

                        </div>
                    </div>


                </div>
                <!--MY CORKBOARD SECTION-->
                <section>
                    <div style="display:flex;">
                        <p class="heading" style="margin-left:10px;">My CorkBoards</p>
                        <a href = "addcorkboard.php"><button style="margin-left: 10px; border-radius: 5px; height: 70%;">Add CorkBoard</button></a>
                    </div>

                    <!--CORKBOARDS OWNED BY USERS-->
                    <div class="dashspace">
                        <p style = "text-align: center;"></p>
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
                            $sql = "SELECT a.board_id, a.visibility, a.board_name, COUNT(b.pushpin_id) AS pushpin_count
                                    FROM corkboard a
                                    LEFT JOIN pushpin b
                                    ON a.board_id = b.board_id
                                    WHERE a.user_id = ?
                                    GROUP BY a.board_id, a.board_name
                                    ORDER BY a.board_name ASC;";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id); // Bind the user_id parameter (integer)
                            $stmt->execute();
                            $result = $stmt->get_result(); // Get the result set

                            // Check if any rows were returned
                            if ($result->num_rows > 0) {
                                // Output each image
                                while($row = $result->fetch_assoc()) {
                                    $board_id = ($row['board_id']);
                                    $board_name = ($row['board_name']);
                                    $pushpin_count = ($row['pushpin_count']);
                                    $visibility = ($row['visibility']);
                                    echo "<a href = 'viewcorkboard.php?board_id=$board_id'><p style = 'text-align: center;'>$board_name ";
                                    if($visibility ==="private"){echo "<span class='private'>(private)</span>";}
                                    echo " with $pushpin_count  Pushpins</p><a>";
                                    //echo $image_url;
                                }
                            }
                            else {
                                echo "<H1 style = 'text-align: center;'>“You have no CorkBoards.</H1>";
                            }

                            // Close the connection
                            $stmt->close();
                            $conn->close();
                        ?>             
                    </div>
                    
                </section>
                <br><br><br>

                <div class="hr"><hr></div>
                <br><br>
                <!--SEARCH FOR PUSHPIN SECTION-->
                <section>
                    <div class="input-group">
                        <input type="search" class="form-control rounded" placeholder="Search description, tags and CorkBoard category" aria-label="Search" aria-describedby="search-addon" />
                        <button type="button" class="btn btn-outline-primary" data-mdb-ripple-init>PushPin Search</button>
                      </div>
                      <br><br>
                </section>

            </div>
        </div>

    </section>
    </div>
    



    <br><br><br>

<script src="assets/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>