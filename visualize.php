<!DOCTYPE html>
<html>
<head>
  <title> ISPC Profiler </title>

  <!-- Angular JS libraries -->
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.0-rc.1/angular.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.0-rc.1/angular-route.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.0-rc.1/angular-animate.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
 <!-- highlight.js -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>

  <link rel="stylesheet" href="css/code.css">
  <link rel="stylesheet" href="css/profile.css">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>


</head>

<body>
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
  $dir_name = date_timestamp_get($date);

  // Get the uploaded source file and profiler data
  $code_path = upload("code", "code/".$dir_name);
  if ($code_path == NULL) {
    // Use the default example path
    $code_path = "code/example.ispc";
    $profile_path = "profile/example.profile";
  } else {
    $profile_path = upload("profile", "profile/".$dir_name);
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

<nav class="navbar navbar-default  navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">ISPC Performance Monitoring for Dummies</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" id="task_menu_wrapper" data-toggle="dropdown" role="button" aria-expanded="false"> Choose Task  <span class="caret"></span></a>
          <ul class="dropdown-menu" id="task_menu" role="menu">
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<div class="container">
  <div class="row">
    
    <div class="col-md-12">
            
      <!-- tabs left -->
      <div class="tabbable tabs-left">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#a" data-toggle="tab">Source Code</a></li>
          <li><a href="#b" data-toggle="tab">Gang Statistics</a></li>
          <li><a href="#c" data-toggle="tab">Cache Performance</a></li>
        </ul>
        <div class="tab-content">
         <div class="tab-pane active" id="a">
          
          <!-- Display checkbox -->
          <div class="checkbox">
            <label><input type="checkbox" id="toggle_all" value="" onclick="toggle_all()"   aria-label="..."> Display Overall Lane Usage  </label>
          </div>
          
          <!-- Display source code -->
          <pre> 
            <code class="cpp">
<?php
echo $formatted_code;
?>
            </code>
          </pre>

         <!-- Table displaying stats -->
         <table class="table" id="stat_box" style="display:none; width:20%; position:fixed; top:200px; right:20px">
          <tr></tr>
            <td><strong> Task Number </strong></td>
            <td id="task_value"></td>
          </tr>

          <tr></tr>
            <td><strong> Line Number </strong></td>
            <td id="line_value"></td>
          </tr>
 
          <tr></tr>
            <td><strong> Line Text </strong></td>
            <td id="text_value"></td>
          </tr>

          <tr></tr>
            <td><strong> Instructions per Cycle </strong></td>
            <td id="ipc_value"></td>
          </tr>
  
          <tr>
            <td><strong> L2 Cache Hit Percentage </strong></td>
            <td id="l2_value"></td> 
          </tr>

          <tr>
            <td><strong> L3 Cache Hit Percentage </strong></td>
            <td id="l3_value"></td> 
          </tr>

          </table>

         </div>

         <div class="tab-pane" id="b">
          <!-- iframe to display stats -->
        </div>
        <div class="tab-pane" id="c">
        </div>
        </div>
      </div>
      <!-- /tabs -->
      
    </div>
    
  </div><!-- /row -->
</div>


  <script>hljs.initHighlightingOnLoad();</script>

  <script type="text/javascript">
<?php
  $profile= shell_exec("cat " . $profile_path);
  $profile = str_replace("\"", "\\\"", $profile);
  $profile = str_replace("\n", "", $profile);
  echo "var profile_data = JSON.parse(\"{$profile}\");\n";
?>

  // Processing input file TODO: sort by task id, change default task id
  var task_id = 0;
  profile_data.sort(function(a,b) {return (a['task_']) - (b['task']) } );
  for (i = 0; i < profile_data.length; i++) {
    profile_data[i]['regions'].sort(function(a,b) { return (a['start_line']) - (b['start_line']) } );
  for (var j = 0; j < profile_data[i]['regions'].length; j++) {
    profile_data[i]['regions'][j]['region_id'] = j;
  }
  }
  console.log(profile_data[task_id]['regions']);
  </script>

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
      
      deselect_all_lines();

      search_line(lineNum);

      update_stats(lineNum);
     
      document.getElementById('toggle_all').checked=false;
    }
  </script>

  <script type="text/javascript" src="js/visualize.js"> </script>
  <script type="text/javascript">
    //toggle_all();
    document.getElementById('task_menu').innerHTML = create_task_menu();
    document.getElementById('toggle_all').checked=true;
    toggle_all();
  </script>

</body>

</html>
