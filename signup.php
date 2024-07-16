<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>SplitIt</title>

    <link rel="stylesheet" href="style.css">
  
  </head>
  <body>

    <div class="wrapper">

      <h1>SplitIt</h1>

      <form action="./includes/validation.php" method="post">

        <label for="email">  Please enter your email address:

          <input type="text" name="email" id="email">

        </label>
        <label for="username"> Please Enter your name:
          <input type="text" name="username" id="username"> 
        </label>
        <label> Password
          <input type="password" name="password" id="password">
        </label>

        <button type="submit">Submit</button>
        <button>Login</button>

      </form>

      </div> 
  </body>
</html>
