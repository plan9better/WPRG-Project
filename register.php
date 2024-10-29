<!doctype html>
<html lang="en">

<?php 
include "Bus.php";
// Included later in nav.php
// include "/var/www/html/project/User.php";
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up - BusTracker</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            grotesk: ['Space Grotesk', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>

<body>
  <?php include "nav.php"; ?>

  <?php 
    if (isset($_POST["submit"])) {
      $user = new User($_COOKIE["bussession"]);
      if (
          isset($_COOKIE["bussession"]) &&
          $user->getUid() != false
      ){
          echo "<div id='error'>You are already logged in</div>";
          echo "</body></html>";
          exit();
      }

      if (
          !ctype_alnum($_POST["login"]) ||
          strlen($_POST["login"]) < 2 ||
          strlen($_POST["login"]) > 32
      ) {
          echo "<div id='error'>Illegal username. Make sure you enter only letters and numbers between 2 and 32 characters.</div>";
          exit();
      }
      if ($_POST["password"] != $_POST["conf_password"]) {
          echo "<div id='error'>Passwords are not the same.</div>";
          exit();
      }
      $name = htmlentities($_POST["login"]);
      $password = hash("sha256", $_POST["password"]);
      // for debugging
      // $name = "admin";
      // $password = hash("sha256", "assword");

      $user = User::checkUserExists($name);
      if ($user) {
          echo "<div id='error'>An account with that login already exists.</div>";
          exit();
      } else {
          $ok = User::addUser($name, $password);
          if ($ok) {
              $user = User::login($name, $password);
              // expires in 7 days
              // "/" -> available on the entire website
              echo "<div id='success'>Success. Refresh or go home to see results.</div>";
              setcookie("bussession", $user["session"], time() + 86400 * 7, "/");
          } else {
              echo "<div id='error'>Something went wrong and we could not create your account. Please try again.</div>";
          }
      }
    } 
  ?>

  <main>
    <div class="h-[calc(100dvh-5rem)] flex items-center justify-center">
      <section class="w-full max-w-lg p-8 space-y-12">
        <form action="register.php" method="post" class="w-full max-w-lg space-y-8">
          <h1 class="font-grotesk font-black text-2xl">Create an account</h1>

          <!-- ! Error just for styling purposes. Proper error validation required to proceed. -->
          <div class="space-y-2">
            <div class="ring-2 ring-red-500 bg-red-50 rounded-md h-12 text-lg px-3 flex items-center">
              <label for="username" class="sr-only">Username</label>
              <input type="text" id="username" name="login" placeholder="Username" class="w-full bg-transparent outline-none text-lg font-medium placeholder:text-lg placeholder:font-light">
            </div>
            <span class="flex items-center gap-2 text-red-500 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
              </svg>
              This field is required.
            </span>
          </div>

          <div class="ring-2 ring-black rounded-md h-12 text-lg px-3 flex items-center">
            <label for="password" class="sr-only">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" class="w-full bg-transparent outline-none text-lg font-medium placeholder:text-lg placeholder:font-light">
          </div>

          <div class="ring-2 ring-black rounded-md h-12 text-lg px-3 flex items-center">
            <label for="password_confirmation" class="sr-only">Confirm password</label>
            <input type="password" id="password_confirmation" name="conf_password" placeholder="Confirm password" class="w-full bg-transparent outline-none text-lg font-medium placeholder:text-lg placeholder:font-light">
          </div>

          <input type="submit" value="Sign up" name="submit" class="ring-2 ring-black bg-black text-white font-grotesk font-medium p-3 rounded-md w-full hover:bg-neutral-800 transition-transform active:scale-95">
        </form>

        <p class="font-light text-neutral-500">
          Already have an account? <a href="login.php" class="text-black font-medium hover:underline">Log in</a>
        </p>
      </section>
    </div>
  </main>
</body>

</html>
