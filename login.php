<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post" action="./includes/validationLogin.php">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="password">password</label>
        <input type="password" name="password" id="password" required>
        <button type="submit">Submit</button>
        <button><a href="./signup.php">Signup</a></button>
    </form>
</body>
</html>