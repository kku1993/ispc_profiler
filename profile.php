<!DOCTYPE html>
<html>
<head>
  <title> ISPC Profiler </title>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"</script>
  <link rel="stylesheet" href="css/profile.css">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>

<body>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<?php
  // Get file name, line numbers
  $filename = $_GET["file"];
  $start_line = $_GET["start"];
  $end_line = $_GET["end"];
  $task = $_GET["task"];

  if (!isset($filename) || !isset($start_line) || !isset($end_line) 
      || !isset($task))
    die(1);

  // Call python script to format profile data
  $args = array("profile/" . $filename, strval($task), strval($start_line), 
      strval($end_line));
  $formatted_profile = shell_exec("./format_profile.py " . implode(" ", $args));

  echo $formatted_profile;
?>
</body>

</html>
