<!DOCTYPE html>
<html>
<head>
  <title> ISPC Profiler </title>

  <!-- highlight.js -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>

  <link rel="stylesheet" href="css/code.css">
</head>

<body>
<?php
  // Get the uploaded file by name
  function upload($name, $target_dir) {
    // Check upload file exists
    $f = $_FILES[$name];
    if (!file_exists($f["tmp_name"]) || 
        !is_uploaded_file($f["tmp_name"])) {
      echo "No files uploaded. <br/>";
      return NULL;
    }

    // Assign a random file name to prevent the user from crafting a filename
    // that would allow arbitray code execution when we
    // use the python script to generate the formatted output later on.
    // TODO ensure no 2 user's code could clash
    $date = date_create();
    // $base_name = basename($f["name"]);
    $file_name = date_timestamp_get($date);
    $target_file = $target_dir."/".$file_name;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    if (!move_uploaded_file($f["tmp_name"], $target_file)) {
      echo "Sorry, there was an error uploading your file. <br/>";
      return NULL;
    }

    return $target_file;
  }

  // Get the uploaded source file and profiler data
  $code_path = upload("code", "code");
  if ($code_path == NULL) {
    // Use the default example path
    $code_path = "code/example.ispc";
    $profile_path = "profile/example.profile";
  } else {
    $profile_path = upload("profile", "profile");
    if ($profile_path == NULL) {
      exit(1);
    }
  }

  // File name of the uploaded code and profile files
  $code_name = basename($code_path);
  $profile_name = basename($profile_path);

  // Call python script to format code for display
  $formatted_code = shell_exec("./format_code.py " . $code_path);
?>

  <!-- iframe to display stats -->
  <iframe id='profile_frame' src='profile.php' width='50%' height='100%' style='border:none; float:right;' ></iframe>

  <!-- Display source code -->
  <pre style="width:50%;">
    <code class="cpp">
<?php
echo $formatted_code;
?>
    </code>
  </pre>

  <script>hljs.initHighlightingOnLoad();</script>

  <script type="text/javascript">
    // Handler for when the user clicks on a line.
    function clickedLine(lineNum) {
      // TODO use real task, end line number
<?php
  echo "var profile_name = \"{$profile_name}\";\n";
?>
      var url = "profile.php?task=1&end=10000&start=" + lineNum.toString() + 
          "&file=" + profile_name;
      document.getElementById('profile_frame').src = url;
    }
  </script>

</body>

</html>
