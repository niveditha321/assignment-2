<?php

$dataFile = 'users.json';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $rollNumber = htmlspecialchars($_POST['roll_number']);
    $email = htmlspecialchars($_POST['email']);
    $age = (int)$_POST['age'];
    $place = htmlspecialchars($_POST['place']);
    $userIndex = isset($_POST['user_index']) ? (int)$_POST['user_index'] : -1;

  
    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);
    } else {
        $users = [];
    }

    if ($userIndex >= 0) {
        $users[$userIndex]['name'] = $name;
        $users[$userIndex]['roll_number'] = $rollNumber;
        $users[$userIndex]['email'] = $email;
        $users[$userIndex]['age'] = $age;
        $users[$userIndex]['place'] = $place;
    } else {
        $users[] = ['name' => $name, 'roll_number' => $rollNumber, 'email' => $email, 'age' => $age, 'place' => $place];
    }

    file_put_contents($dataFile, json_encode($users));
}

if (isset($_GET['delete'])) {
    $userIndex = (int)$_GET['delete'];

    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);

        if (isset($users[$userIndex])) {
            unset($users[$userIndex]);
            $users = array_values($users); // Reindex array after deletion
            file_put_contents($dataFile, json_encode($users));
        }
    }
}

$usersData = '';
if (file_exists($dataFile)) {
    $jsonData = file_get_contents($dataFile);
    $users = json_decode($jsonData, true);

    $usersData .= "<h3>Student List:</h3><div class='users-list'>";
    foreach ($users as $index => $user) {
        $usersData .= "<div class='user'><strong>Name:</strong> " . $user['name'] . " (Roll No: " . $user['roll_number'] . ")<br>";
        $usersData .= "<strong>Email:</strong> " . $user['email'] . "<br>";
        $usersData .= "<strong>Age:</strong> " . $user['age'] . "<br>";
        $usersData .= "<strong>Place:</strong> " . $user['place'] . "<br>";
        $usersData .= "<a href='?delete=$index' onclick='return confirm(\"Are you sure?\");'>Delete</a> | ";
        $usersData .= "<a href='?edit=$index'>Edit</a><br><br></div>";
    }
    $usersData .= "</div>";
}

$editName = '';
$editRollNumber = '';
$editEmail = '';
$editAge = '';
$editPlace = '';
$editIndex = -1;
if (isset($_GET['edit'])) {
    $editIndex = (int)$_GET['edit'];

    if (file_exists($dataFile)) {
        $jsonData = file_get_contents($dataFile);
        $users = json_decode($jsonData, true);

        if (isset($users[$editIndex])) {
            $editName = $users[$editIndex]['name'];
            $editRollNumber = $users[$editIndex]['roll_number'];
            $editEmail = $users[$editIndex]['email'];
            $editAge = $users[$editIndex]['age'];
            $editPlace = $users[$editIndex]['place'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP JSON CRUD with Roll Number After Name</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container { margin: 20px; }
        .users-list { margin-top: 20px; }
        .user { margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <form method="POST">
        <input type="hidden" name="user_index" value="<?php echo $editIndex; ?>">
        Name: <input type="text" name="name" value="<?php echo $editName; ?>" required><br>
        Roll Number: <input type="text" name="roll_number" value="<?php echo $editRollNumber; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $editEmail; ?>" required><br>
        Age: <input type="number" name="age" value="<?php echo $editAge; ?>" required><br>
        Place: <input type="text" name="place" value="<?php echo $editPlace; ?>" required><br>
        <input type="submit" value="<?php echo $editIndex >= 0 ? 'Update' : 'Submit'; ?>">
    </form>

    <button id="toggleButton">Display Data</button>

    <div id="userData">
        <?php echo $usersData; ?>
    </div>
</div>

<script>
    document.getElementById("toggleButton").addEventListener("click", function() {
        var userDataDiv = document.getElementById("userData");
        if (userDataDiv.style.display === "none" || userDataDiv.style.display === "") {
            userDataDiv.style.display = "block";  
            this.textContent = "Hide Data";  
        } else {
            userDataDiv.style.display = "none";  
            this.textContent = "Display Data";  
        }
    });
</script>

</body>
</html>
