<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>baseManager</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./css/style.css">
  
  </head>
  <body>

  <div class="formInput">
  <form action="./includes/validationSignup.php" method="post" class="loginSignup">
    <h1 class="logo">BaseManager</h1>

    <div class="inputContainer">
      <label for="email">Please enter your email address:</label>
      <input type="text" name="email" id="email" placeholder="example@gmail.com" required>
    </div>

    <div class="inputContainer">
      <label for="username">Please Enter your name:</label>
      <input type="text" name="username" id="username" placeholder="John" required>
    </div>

    <div class="inputContainer">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>

    <button type="submit" class="form-button">Submit</button>
    <div class="signup-button">
      <a href="./login.php">Login</a>
    </div>
  </form>
</div>

  </body>
</html>
