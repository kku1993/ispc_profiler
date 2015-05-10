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
  if(!isset($_GET["upload_id"]) || !isset($_GET["code"])) {
    echo "Using example code and profile.<br/><br/>";
    $code_path = "code/example.ispc";
    $profile_path = "profile/example.profile";
  } else {
    $upload_id = $_GET["upload_id"];
    $code_name = $_GET["code"];
    $code_dir = "code/" . $upload_id;
    $code_path = "code/" . $upload_id ."/" . $code_name;
    $profile_path = "profile/" . $upload_id . "/profile_file";
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
      <a class="navbar-brand" href="index.html">ISPC Performance Monitoring for Dummies</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li>
          <!-- Space to display the current task -->
          <a href="#" id="task_menu_wrapper" role="button" aria-expanded="false">Task 0</a>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" id="source_dropdown" data-toggle="dropdown" role="button" aria-expanded="false"> Source File <span class="caret"></span></a>
          <ul class="dropdown-menu" id="source_menu" role="menu">
<?php
  // Output source file selections
  $files = scandir($code_dir);
  foreach ($files as $f) {
    if ($f == "." || $f == "..")
      continue;
    
    $file_name = basename($f);
    echo "<li><a href='visualize.php?upload_id={$upload_id}&code={$file_name}'>{$file_name}</a></li>";
    echo "<li class='divider'></li>";
  }
?>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" id="task_menu_wrapper" data-toggle="dropdown" role="button" aria-expanded="false"> Choose Task  <span class="caret"></span></a>
          <ul class="dropdown-menu" id="task_menu" role="menu">
          </ul>
        </li>
      </ul>
<!--
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
-->
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
          <li><a href="#b" data-toggle="tab">Suggestions</a></li>
          <li><a href="#c" data-toggle="tab">Credits</a></li>
        </ul>
        <div class="tab-content">
         
         <div class="tab-pane" id="c">
          <label>15-418 Final Project</label><br>
          <label>Kevin Ku</label><br>
          <label>Stephen Choi</label>
         </div>

         <div class="tab-pane" id="b">
         </div>

         <div class="tab-pane active" style="width:85%" id="a">
          
          <!-- Display checkbox -->
          <div class="checkbox">
            <label><input type="checkbox" id="toggle_all" value="" onclick="toggle_all()"   aria-label="..."> Display Overall Lane Usage  </label>
          </div>
          
          <!-- Display source code -->
<h3>
<?php
echo $code_name;
?>
</h3>
          <pre> 
            <code class="cpp">
<?php
echo $formatted_code;
?>
            </code>
          </pre>


         <div style="width:30%; position:fixed; top:50px; right:50px">

         <!-- Table displaying color options -->
         <table class="table" id="color_table">
          <caption>Color Key</caption>
          <tr></tr>
            <td><strong> Lane Usage of Line </strong></td>
            <td><strong> Color Displayed </strong></td>
          </tr>
  
          <tr>
            <td> Function Call </td>
            <td id="color_function"></td> 
          </tr>
          <tr>
 
          <td> x < 30% </td>
            <td id="color_bad"></td> 
          </tr>

          <tr>
            <td> 30% <= x <=  70%  </strong></td>
            <td id="color_okay"></td> 
          </tr>

          <tr>
            <td> 70% <= x < 100% </td>
            <td id="color_optimal"></td> 
          </tr>

          <tr>
            <td> x = 100% </td>
            <td id="color_perfect"></td> 
          </tr>

          </table>

         <!-- Table displaying stats -->
         <table class="table" id="stat_box" style="display:none; overflow-y:scroll; height:450px">
          <caption> Statistics Table </caption>
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
    var file_names = [];
<?php
  $profile= shell_exec("cat " . $profile_path);
  $profile = str_replace("\"", "\\\"", $profile);
  $profile = str_replace("\n", "", $profile);
  echo "var profile_data = JSON.parse(\"{$profile}\");\n";
  echo "var source_file = \"{$code_name}\";\n";
  // Create filename array
  $files = scandir($code_dir);
  foreach ($files as $f) {
    if ($f == "." || $f == "..")
      continue;
    
    $file_name = basename($f);
    echo "file_names.push(\"{$f}\");\n";
  }
?>

  // Processing input file TODO: sort by task id, change default task id
  var task_id = 0;
  profile_data.sort(function(a,b) {return (a['task']) - (b['task']) } );
  for (i = 0; i < profile_data.length; i++) {
    profile_data[i]['regions'].sort(function(a,b) { return (a['start_line']) - (b['start_line']) } );
    for (var j = 0; j < profile_data[i]['regions'].length; j++) {
      profile_data[i]['regions'][j]['region_id'] = j;
    }
  }

  var complete_profile = JSON.parse(JSON.stringify(profile_data));
  var profile_data_array = new Object();

  // For each file
  for (i = 0; i < file_names.length; i++) {
    var copy = JSON.parse(JSON.stringify(profile_data));
    // For each task
    for (j = 0; j < copy.length; j++) {
      // For each region, check whether it's in the deserved file
      copy[j]['regions'] = copy[j]['regions'].filter(
      function(x) { 
        console.log(x.file_name, file_names[i]);
        return x.file_name == file_names[i]
      });
      /*
      for (k = 0; k < copy[j]['regions'].length; k++) {
        if (copy[j]['regions'][k]['file_name'] != file_names[i]) {
          console.log("DELETED", copy[j]['regions'][k]['file_name'],file_names[i]);
          //delete copy[j]['regions'][k];
          copy[j]['regions'] = copy[j]['regions'].splice(k,1);
         }
      }
      */
    }
  profile_data_array[file_names[i]] = copy;
  }

  console.log(source_file);
  profile_data = profile_data_array[source_file];
  console.log("check this ", profile_data);
  /*console.log(file_names.length);
  console.log(file_names);
  console.log(profile_data_array);
  console.log(profile_data_array[file_names[1]][1]['regions'].length, profile_data_array[file_names[1]][1]['regions']);
  */
  </script>

  <script type="text/javascript">
    // Handler for when the user clicks on a line.
    function clickedLine(lineNum) {
      // TODO use real task, end line number
<?php
  echo "var profile_name = \"{$profile_name}\";\n";
?>
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
    document.getElementById('b').innerHTML = create_tips();
    document.getElementById('toggle_all').checked=true;
    toggle_all();
  </script>

</body>

</html>
