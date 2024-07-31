<?php
session_start();
if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: home.php");
    exit(); 
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include_once "./includes/database.inc.php";

// Check if $pdo is set
if ($pdo === null) {
    die("Connection failed.");
}

// Updated SQL query to reflect the new database schema

try {
    $sql = "SELECT g.groupName, g.groupId
        FROM user_group ug
        JOIN groups g ON ug.groupId = g.groupId
        WHERE ug.userId=:userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $_SESSION['userId']);
    $stmt->execute();

    $Groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $Groups = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./css/style.css">
<style>

        .dataInput {
            display: none; /* Hide form initially */
            position: fixed;
            top: 0;
            left: 0;
            background-color: black;
            width: 100vw;
            text-align: center;
            font-size: 1.6rem;
            color: white;
        }
        .centering{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        }
        .group-form{

        background-color: #1E1E1E;
        padding: 3.2rem;
        border-radius: 0.8rem;
        box-shadow: 0 0.4rem 0.8rem rgba(3, 3, 3, 0.3);
        max-width: 40rem;
        width: 100%;

        }
        .inputContainer {
        padding-right: 2rem;
    }

        .group-form label {
            display: block;
            margin-bottom: 0.8rem;
            color: #fff;
        }

        .group-form input {
        width: 100%;
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 0.4rem;
        background-color: #333;
        color: #fff;
        font-family: 'Inter', sans-serif;
        }

        .group-form .groupSubmit {
            width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 0.4rem;
        background-color: #7C90DB;
        color: #fff;
        font-size: 1.6rem;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
        }
        .group-form .groupSubmit:hover {
        background-color: #4a5683;
    }

        .group-form .close-button {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 10;
            padding: 1rem;
        border: none;
        border-radius: 0.4rem;
        background-color: #7C90DB;
        color: #fff;
        font-size: 1.6rem;
        cursor:pointer;

        }
        .group-form .close-button:hover{
            background-color: #4a5683;
        }

        .createGroup {
            /* display: block; */
            padding: 1rem 2rem;
            height:20%;
            font-size: 1.5rem;
            background-color: #7C90DB;
            color: white;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .createGroup:hover {
            background-color: #4a5683; /* Using a shade of the primary color for the hover effect */
        }
        .logo {
        font-family: "Platypi", serif;
        color:#7C90DB;
        margin-bottom:3.2rem;
    }

    /*  group data display */
    .groupDisplay {
    display:flex;
    /* align-items:center; */
    justify-content:space-between;
    flex-direction:row-reverse;
    padding:2rem 2rem;
    

}

.groupDisplay ul {
    list-style: none; /* Remove default list styling */
    padding: 0;
}

.groupDisplay li {
    margin-bottom: 2rem;
}
.groupDisplay li a{
            padding: 0.4rem 0.4rem;
            font-size: 1.6rem;
            background-color: #7C90DB;
            color: white;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
}
.containerz{
background-color:#1E1E1E;
}
    </style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
      $(function () {
        const search=$(".createGroup")
        search.on("click",()=>{
          console.log("hi")
          $(".dataInput").fadeIn();
          $("[name='groupName']").focus();
        })
        const close= ()=>{
          $(".dataInput").fadeOut();
          $(".createGroup").focus();}

        $(".close-button").on("click", close)
      
          $(document).on("keydown", (e) => {
        if (e.key === "Escape") {
          close();
        }
      });
    })

    </script>
</head>
<body>
    <?php
include_once "./navbar.php";
?>
<div class="containerz">
<div class="dataInput">
    <div class="centering">
<form method="post" action="./includes/validationForGroup.php" class="group-form">
<h1 class="logo">BaseManager</h1>
<div class="inputContainer">
        <label for="GroupName">GroupName</label>
        <input type="text" name="groupName" id="groupName" required>
</div>
        <button class ="groupSubmit"type="submit">Submit</button>
        <button type="button" class="close-button">Close</button>
    </form>
</div>
</div>
<div class="groupDisplay">
    <button class="createGroup">Create a new Group</button>

    <?php
    if (!empty($Groups)) {
        echo "<ul>";
        $index = 1; // Initialize index for numbering
        foreach ($Groups as $group) {
            echo "<li>$index. <a href='group.php?groupId={$group['groupId']}&groupName={$group['groupName']}'>" . htmlspecialchars($group["groupName"]) . "</a></li>";
            $index++; // Increment index
        }
        echo "</ul>";
    } else {
        echo "0 results";
    }

    $pdo = null; 
    ?>
</div>
</body>
</html>
