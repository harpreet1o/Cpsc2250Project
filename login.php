<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./css/style.css">
</head>


<body>
    <div class="formInput">
    <form method="post" action="./includes/validationLogin.php" class="loginSignup">
    <h1 class="logo">BaseManager</h1>
        <div class="inputContainer">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="example@gmail.com" required>
    </div>
    <div class="inputContainer">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
    </div>
        <button type="submit" class="form-button">Submit</button>
        <div class="signup-button">
            <a href="./signup.php">Signup</a>
        </div>
    </form>
    </div>
</body>
</html>