<?php
  // Get the uploaded file by name
  function upload($name, $target_dir) {
    // Check upload file exists
    $f = $_FILES[$name];

    $files = array();
    $fdata = $_FILES[$name];
    if (is_array($fdata['name'])) {
      for($i = 0; $i < count($fdata['name']); ++$i){
        if (!file_exists($fdata["tmp_name"][$i]) || 
            !is_uploaded_file($fdata["tmp_name"][$i])) {
          echo "No files uploaded. <br/>";
          return NULL;
        }

        $files[]=array(
          'name'    =>$fdata['name'][$i],
          'type'  => $fdata['type'][$i],
          'tmp_name'=>$fdata['tmp_name'][$i],
          'error' => $fdata['error'][$i], 
          'size'  => $fdata['size'][$i]  
          );
      }
    } else 
      $files[] = $fdata;

    // Make output dir
    if (!mkdir($target_dir, 0777)) {
      echo "Failed to mkdir<br/>";
    }

    $tmp_names = array();
    foreach ($files as $f) {
      if ($name == "code") {
        $file_name = basename($f["name"]);
        $target_file = $target_dir."/".$file_name;

        if (!move_uploaded_file($f["tmp_name"], $target_file)) {
          echo "Sorry, there was an error uploading your file. <br/>";
          return NULL;
        }
      } else {
        // Concatenate all profile into 1 file.
        $tmp_names[] = $f["tmp_name"];
      }
    }

    if ($name == "profile") {
      // Concatenate all profile into 1 file.
      $target_file = $target_dir."/profile_file"; 
      $args = implode(" ", $tmp_names);
      shell_exec("./concat_profile.py " . $args . " " . $target_file);
    }

    return $target_file;
  }

  // Tmp directory name to store the uploaded files
  $date = date_create();
  $upload_id = date_timestamp_get($date);

  // Get the uploaded source file and profiler data
  $code_path = upload("code", "code/".$upload_id);
  if ($code_path == NULL) {
    // Use the default example path
    header( 'Location: visualize.php' ) ;
  } else {
    $profile_path = upload("profile", "profile/".$upload_id);
    if ($profile_path == NULL) {
      echo "<script type='text/javascript'>alert('Upload Failed');</script>";
      header( 'Location: index.html' ) ;
    } else {
      // Pick random source file to display first
      header( 'Location: visualize.php?upload_id='. $upload_id . "&code=" . basename($code_path)) ;
    }
  }
?>
