<?php
$username = $_SESSION['username'];
?>
<style>button a {
        text-decoration: none;
        color:  white;
        font-size: 1.6rem; /* Adjusted font size to match other navbar links */
        font-family: 'Inter', sans-serif;
    }
    
    </style>
<div class="navbar">
    <h1 class="logo"><a href="home.php">BaseManager</a></h1>
    <div>
        <!-- <span class="material-symbols-sharp" id="toggle-theme">
            toggle_off
        </span> -->
        <p class="logo">Hi, <?php echo $username ?></p>
        <button class="login-button"><a href="documentation.php">Documentation</a></button>
        <form method="post" style="display:inline;">
            <button type="submit" name="logout" class="login-button">Logout</button>
        </form>
        
        
    </div>
</div>
