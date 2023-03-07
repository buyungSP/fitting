<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/mdb.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="css/masin.css" rel="stylesheet"/>
  <script type="text/javascript" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" ></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/bootstrap.js"></script>
  <script type="text/javascript" src="js/mdb.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script type="text/javascript" src="js/masin.js?v=113"></script>
  <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
</head>
<body class="d-flex flex-column h-100">
<?php
  	require 'koneksi.php';
    new Login();
  exit;
?>
</body>
</html>