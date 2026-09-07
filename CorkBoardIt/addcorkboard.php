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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $board_name = $_POST['board_name'];
    $board_category = $_POST['board_category'];
    $visibility = $_POST['visibility'];
    $board_password = ($visibility == 'private') ? $_POST['board_password'] : null;

    // Validate form inputs
    if (empty($board_name) || empty($board_category) || empty($visibility)) {
        echo "Please fill in all required fields.";
    } else {
        // Prepare the SQL query to insert the data
        $stmt = $conn->prepare("INSERT INTO corkboard (user_id, board_name, board_category, visibility, board_password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $board_name, $board_category, $visibility, $board_password);

        // Execute the query
        if ($stmt->execute()) {
            //echo "<p class='success'>Board created successfully!</p>";
            $board_id = $conn->insert_id;
            
            header("Location: viewcorkboard.php?board_id=" . $board_id);
            exit();

        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        // Close the statement
        $stmt->close();
    }
}







// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Corkboard</title>
    <style>
        /* Basic page styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
            color: #444;
        }

        /* Form container styling */
        .form-container {
            background-color: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        /* Input and label styling */
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        /* Submit button styling */
        input[type="submit"] {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        /* Error/success message styling */
        .error {
            color: red;
            text-align: center;
        }

        .success {
            color: green;
            text-align: center;
        }

        /* Password field container */
        #passwordField {
            display: none;
        }

        /* Media queries for responsive design */
        @media (max-width: 480px) {
            .form-container {
                width: 90%;
            }
        }
    </style>

    <script>
        // Show or hide the password field based on visibility selection
        function togglePasswordField() {
            var visibility = document.querySelector('input[name="visibility"]:checked').value;
            var passwordField = document.getElementById('passwordField');
            if (visibility === 'private') {
                passwordField.style.display = 'block';
            } else {
                passwordField.style.display = 'none';
            }
        }
    </script>
</head>
<body>

<div class="form-container">
    <h2>Create Corkboard</h2>
    <form method="POST">
        <label for="board_name">Title:</label>
        <input type="text" id="board_name" name="board_name" required><br>

        <label for="board_category">Board Category:</label>
        <select id="board_category" name="board_category" required>
            <option value="Education">Education</option>
            <option value="People">People</option>
            <option value="Sports">Sports</option>
            <option value="Other">Other</option>
            <option value="Architecture">Architecture</option>
            <option value="Travel">Travel</option>
            <option value="Pets">Pets</option>
            <option value="Food & Drink">Food & Drink</option>
            <option value="Home & Garden">Home & Garden</option>
            <option value="Photography">Photography</option>
            <option value="Technology">Technology</option>
            <option value="Art">Art</option>
        </select><br>

        <label for="visibility">Visibility:</label><br>
        <input type="radio" id="public" name="visibility" value="public" onclick="togglePasswordField()" required>
        <label for="public">Public</label><br>
        <input type="radio" id="private" name="visibility" value="private" onclick="togglePasswordField()" required>
        <label for="private">Private</label><br><br>

        <div id="passwordField">
            <label for="board_password">Board Password:</label>
            <input type="password" id="board_password" name="board_password"><br>
        </div>

        <input type="submit" value="Add">
    </form>
</div>

</body>
</html>
