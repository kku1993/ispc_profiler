<!DOCTYPE html>
<html>
<head>
  <title> ISPC Profiler </title>

  <link rel="stylesheet" href="css/profile.css">
</head>

<body>
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
