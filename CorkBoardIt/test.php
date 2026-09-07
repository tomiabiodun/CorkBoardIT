<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("No user is logged in");
}

$host = 'localhost';
$db = 'CorkBoardIt';
$user = 'root';
$pass = 'abcd1234';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['first_name'];
$lastname = $_SESSION['last_name'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorkBoardIt</title>
    <link href="assets/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <section>
            <p>CorkBoardIt Home Page</p>
            <div class="bg-white p-3 rounded">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Home Page for</h5>
                        <h4><?php echo $firstname . ' ' . $lastname; ?></h4>
                    </div>
                    <div>
                        <img src="assets/image/logo2.png" alt="Logo" class="img-fluid" style="max-width: 60%;">
                    </div>
                </div>
                <hr class="hr">
                
                <!--RECENT CORKBOARD UPDATES BEGINS-->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="heading">Recent CorkBoard Updates</p>
                        <button class="btn">Popular Tags</button>
                    </div>

                    <!--DISPLAY RECENT CORKBOARDS-->
                    <div class="mt-4">
                        <!-- Add your CorkBoard update display here -->
                    </div>

                    <!--MY CORKBOARD SECTION-->
                    <section class="mt-5">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="heading">My CorkBoards</p>
                            <a href="addcorkboard.php"><button class="btn">Add CorkBoard</button></a>
                        </div>

                        <!--CORKBOARDS OWNED BY USERS-->
                        <div class="dashspace mt-3">
                            <p class="text-center"></p>
                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "abcd1234";
                            $dbname = "CorkBoardIt";

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $sql = "SELECT a.board_id, a.visibility, a.board_name, COUNT(b.pushpin_id) AS pushpin_count
                                    FROM corkboard a
                                    LEFT JOIN pushpin b
                                    ON a.board_id = b.board_id
                                    WHERE a.user_id = ?
                                    GROUP BY a.board_id, a.board_name
                                    ORDER BY a.board_name ASC;";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $board_id = ($row['board_id']);
                                    $board_name = ($row['board_name']);
                                    $pushpin_count = ($row['pushpin_count']);
                                    $visibility = ($row['visibility']);
                                    echo "<a href='viewcorkboard.php?board_id=$board_id'><p class='text-center'>$board_name ";
                                    if ($visibility === "private") {
                                        echo "<span class='text-danger'>(private)</span>";
                                    }
                                    echo " with $pushpin_count Pushpins</p></a>";
                                }
                            } else {
                                echo "<h1 class='text-center'>You have no CorkBoards.</h1>";
                            }

                            $stmt->close();
                            $conn->close();
                            ?>             
                        </div>
                    </section>

                    <hr class="hr">
                    
                    <!--SEARCH FOR PUSHPIN SECTION-->
                    <section class="mt-4">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search description, tags and CorkBoard category">
                            <button type="button" class="btn btn-outline-primary">PushPin Search</button>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

<script src="assets/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
