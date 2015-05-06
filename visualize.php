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
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
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
          <!-- Display source code -->
          <pre> 
            <code class="cpp">
<?php
echo $formatted_code;
?>
              </code>
          </pre>
         </div>
         <div class="tab-pane" id="b">
          <!-- iframe to display stats -->
          <iframe id='profile_frame' src='profile.php' width='50%' height='100%' style='border:none; float:right;' ></iframe>

        </div>
        <div class="tab-pane" id="c">Thirdamuno, ipsum dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. 
         Quisque mauris augue, molestie tincidunt condimentum vitae. </div>
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
  profile_data['regions'].sort(function(a,b) { return (a['start_line']) - (b['start_line']) } );
  for (var i = 0; i < profile_data['regions'].length; i++) {
    profile_data['regions'][i]['region_id'] = i;
  }
  console.log(profile_data['regions']);
  </script>

  <script type="text/javascript">
  function search_line(lineNum) {
    for (var i = 0; i < profile_data['regions'].length; i++) {
      // Found the region this click belongs to
      if (profile_data['regions'][i]['start_line'] > lineNum || i == (profile_data['regions'].length-1)) {
        // If it's prior to the first start_line then only highlight the line
        if (i == 0) {
          console.log(lineNum);
          document.getElementById('line_'+lineNum.toString()).className += "selected_line ";
          document.getElementById('line_'+lineNum.toString()).setAttribute('line_color', 'yellow');
          console.log("OWO");
          break;
        }
        // If contained within this region, highlight entire region
        if (lineNum <= profile_data['regions'][i-1]['end_line']) {
          for (var j = profile_data['regions'][i-1]['start_line']; j < (profile_data['regions'][i-1]['end_line']+1); j++) {
            console.log("JELLY")
              highlight_line(i-1,j);
            console.log("END OF JELLY");
          }
        }
        // If not, only highlight the line
        else {
          console.log("line 292 else case");
          document.getElementById('line_'+lineNum.toString()).className += "selected_line ";
          document.getElementById('line_'+lineNum.toString()).setAttribute('line_color', 'yellow');
        }
        break;
      }
    }
  }

function highlight_line(region_id, line_number) {  
  console.log("start",region_id," ",line_number);
  document.getElementById('line_'+line_number.toString()).className += "selected_line ";
  console.log("wtf");

  if (profile_data['regions'][region_id].lane_usage.length == 0) {
    console.log('length too small');
    return;
  }

  // If youre fall in the first part of the lane usage
  if (line_number <= profile_data['regions'][region_id].lane_usage[1].line) {
    if (profile_data['regions'][region_id].lane_usage[0].percent < 30) { 
      console.log("hohoho", document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'black');

      //document.getElementById('line_'+line_number.toString()).line_color = 'black');
      console.log('black');
      console.log("rororo",document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
    }
    else if (profile_data['regions'][region_id].lane_usage[0].percent < 70) {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'green');
      console.log('green');
    }
    else {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'red');
      console.log('red');
    }
  }
  // If youre fall in the second part of the lane usage
  else {
    console.log("ELSE HAAAAAAAAA");
    if (profile_data['regions'][region_id].lane_usage[1].percent < 30) {       i
      console.log('black');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'black');
    }
    else if (profile_data['regions'][region_id].lane_usage[1].percent < 70) {
      console.log('green');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'green');
    }
    else {
      console.log('red');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'red');
    }
  }
}



    // Handler for when the user clicks on a line.
    /*
    var source_code = document.getElementsByClassName("source_code");
    for (var i = 0; i < source_code.length; i++) {
      search_line(i);
    }
    */

    function clickedLine(lineNum) {
      // TODO use real task, end line number
<?php
  echo "var profile_name = \"{$profile_name}\";\n";
?>
      var url = "profile.php?task=1&end=10000&start=" + lineNum.toString() + 
          "&file=" + profile_name;
      document.getElementById('profile_frame').src = url;
      
      // Stop selecting all previous line
      var previously_selected = document.getElementsByClassName("selected_line ");
      //for (var i=0; i < previously_selected.length; i++) {
      while (previously_selected.length > 0) {
        console.log("REMOVING LINE");
        previously_selected[0].className = 
          previously_selected[0].className.replace( /(?:^|\s)selected_line(?!\S)/g , '' );
      }

      search_line(lineNum);

      console.log("ksandlksad",document.getElementById('line_'+lineNum.toString()).getAttribute('line_color'));
      console.log("FINISHED CLICK");
      //document.getElementById('line_'+lineNum.toString()).className += "selected_line";
    }
  </script>

</body>

</html>
