<!doctype html>
<html lang="en">

<?php include "Bus.php"; ?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Tracker</title>
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
  <!-- Primary navigation -->
  <?php include "nav.php"; ?>

  <main>
    <!-- Jumbotron -->
    <?php include "jumbotron.php"; ?>

    <!-- Call to action -->
    <section class="bg-[#55f458] px-8">
      <div class="container mx-auto flex flex-col items-center py-16 text-center gap-8">
        <h2 class="font-grotesk font-black text-4xl">Save your favorite routes and <br> get personalized alerts!</h2>
        <button class="block bg-black text-white p-4 rounded-md font-grotesk text-lg font-medium transition-transform active:scale-95 hover:bg-black/80">Sign up now</button>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-black p-32">
    <div class="container mx-auto">
    </div>
  </footer>
</body>
</html>
