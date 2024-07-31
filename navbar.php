<?php
$username = $_SESSION['username'];
?>
<div class="navbar">
<h1 class="logo"><a href="home.php">BaseManager</a></h1>
    <div>
    <span class="material-symbols-sharp" id="toggle-theme">
                toggle_off
            </span>
    <p class="logo">Hi, <?php echo $username ?></p>
    <form method="post" style="display:inline;">
            <button type="submit" name="logout" class="login-button">Logout</button>
        </form>
</div>
</div>