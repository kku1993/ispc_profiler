function get_region(lineNum) {
  var regions = profile_data[task_id]['regions'];
  var latest = -1;
  for (var i = 0; i < regions.length; i++) {
    if (regions[i].start_line <= lineNum && lineNum <= regions[i].end_line)
      latest = i;
  }
  return latest;
}

function search_line(lineNum) {
  console.log("searching");
  for (var i = 0; i < profile_data[task_id]['regions'].length; i++) {
    // Found the region this click belongs to
    if (profile_data[task_id]['regions'][i]['start_line'] > lineNum) { // || i == (profile_data[task_id]['regions'].length-1)) {
      // If it's prior to the first start_line then only highlight the line
      if (i == 0) {
        console.log(lineNum);
        document.getElementById('line_'+lineNum.toString()).className += " selected_line ";
        document.getElementById('line_'+lineNum.toString()).setAttribute('line_color', 'undefined');
        console.log("OWO");
        return;
      }
      // Check each region before, similar to popping off a stack
      for (var region_id = i-1; region_id >= 0; region_id--) {
        if (lineNum <= profile_data[task_id]['regions'][region_id]['end_line']) {


          for (var j = profile_data[task_id]['regions'][region_id]['start_line']; j < (profile_data[task_id]['regions'][region_id]['end_line']+1); j++) {
              highlight_line(region_id,j);
         } 
         return;
        }
      }

      // Case: in between two regions, not in any
        console.log("line 292 else case");
        var chosenLine =  document.getElementById('line_'+lineNum.toString());
        chosenLine.className += " selected_line ";
        chosenLine.setAttribute('line_color', 'undefined');
        return;
        
    }
  }
  // It's either after the last region or in the last region
    if (profile_data[task_id]['regions'].length == 0) {
      console.log("nothing happens");
      return;
    }
    var last_region = profile_data[task_id]['regions'].slice(-1)[0];
    console.log("40, test", last_region['end_line'],lineNum);
    if (last_region['end_line'] >= lineNum) {
      console.log("41 did not get here");
      for (var j = last_region['start_line']; j < (last_region['end_line']+1); j++) {
          highlight_line(profile_data[task_id]['regions'].length-1,j);
      }
      return;
    }
    // If it's not in the last region, just choose as selected anyway
    var chosenLine =  document.getElementById('line_'+lineNum.toString());
    chosenLine.className += " selected_line ";
    chosenLine.setAttribute('line_color', 'undefined');
    console.log("not in last region lineNum ->", lineNum);
}

function highlight_line(region_id, line_number) {  
  /*
  var our_line = document.getElementById('line_'+line_number.toString());
  console.log("abc", our_line.innerHTML.indexOf("else")); 
  if ((our_line.innerHTML.indexOf("else ") != -1) || (our_line.innerHTML).indexOf("if ") > -1) {
    console.log("RETURNING");
    our_line.className = "";
    return;
  }
  */
  document.getElementById('line_'+line_number.toString()).className += " selected_line ";
  var my_region = profile_data[task_id]['regions'][region_id];

  if ((my_region.region_type == 32) && (my_region.lane_usage.length == 0))  {
    console.log("CMON");
    var bin_string = (my_region.initial_mask >>> 0).toString(2)
    var count = 0;
    var total = 0;
    for (var i = 0; i < bin_string.length; i++) {
      if (bin_string[i] == 1) {
        count++;
      }
      total++;
    }
    my_region.lane_usage.push({line:my_region.start_line, percent:(count/total*100) });
    console.log("NEW THING", count/total*100);
  }

  if (profile_data[task_id]['regions'][region_id].lane_usage.length == 0) {
    console.log('length too small in highlight_line');
    document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'no_branch');
    return;
  }

  // Only one lane, TODO: Make more generic for multiple possible lanes
  if (profile_data[task_id]['regions'][region_id].lane_usage.length == 1) { 
    if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 30) { 
      //console.log("hohoho", document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'bad');
      console.log("82, bad")
      //document.getElementById('line_'+line_number.toString()).line_color = 'bad');
      console.log('highlighting bad');
      //console.log("rororo",document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
    }
    else if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 70) {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'okay');
      console.log('89 okay');
    }
    else {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'optimal');
      console.log('93, optimal');
    }
  return;
  }

  console.log("Two lanes in highlight_line");

  // TODO: if line is not included, do nothing?
  if (line_number < my_region.lane_usage[0].line) {  
    console.log("102, line is not in this region");
    //document.getElementById('line_'+line_number.toString()).className.replace( /(?:^|\s)selected_line(?!\S)/g , '' );
    document.getElementById('line_'+line_number.toString()).className -= "selected_line";
    return;
  }

 for (var r = my_region.lane_usage.length-1; r >= 0; r--) { 
   if (line_number >= my_region.lane_usage[r].line) {
      if (profile_data[task_id]['regions'][region_id].lane_usage[r].percent < 30) { 
        //console.log("hohoho", document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
        document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'bad');
        return;
        //document.getElementById('line_'+line_number.toString()).line_color = 'bad');
        console.log('115 bad');
        //console.log("rororo",document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
      }
      else if (profile_data[task_id]['regions'][region_id].lane_usage[r].percent < 70) {
        document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'okay');
        console.log("abc", document.getElementById('line_'+line_number.toString()).innerHTML.indexOf("else")); 
        console.log('120 okay line# ->', line_number);
        return;
      }
      else {
        document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'optimal');
        console.log('124 optimal, line_num ->', line_number);
        return;
      }
    }
  }
  console.log("HOW");
}
  
   function deselect_all_lines() {
      var previously_selected = document.getElementsByClassName("selected_line ");
      while (previously_selected.length > 0) {
        console.log("REMOVING LINE");
        previously_selected[0].className = 
          previously_selected[0].className.replace( /(?:^|\s)selected_line(?!\S)/g , '' );
      }
   }



   function toggle_all() {
    var elem = document.getElementById('toggle_all');
    if(elem.checked) {
      for (var i = 0; i < profile_data[task_id]['regions'].length; i++) {
        console.log("OUR START LINE IS ", profile_data[task_id]['regions'][i].start_line);
        search_line(profile_data[task_id]['regions'][i].start_line);
      }
   }
    else {
      deselect_all_lines();
    }
    var box = document.getElementById('stat_box');
    box.style.display = "none";
   }

function update_stats(lineNum) {
  var box = document.getElementById('stat_box');
  for (var i = 0; i < profile_data[task_id]['regions'].length; i++) {
    // Line belongs to region prior to region i
    if (profile_data[task_id]['regions'][i]['start_line'] > lineNum) {
      // If it's prior to the first start_line then only highlight the line
      if (i == 0) {
        console.log("WHY U NO HIDE?");
        box.style.display = "none";
        return;
      }

      for (var region_id = i-1; region_id >= 0; region_id--) {
        if (lineNum <= profile_data[task_id]['regions'][region_id]['end_line']) {
          var my_region = profile_data[task_id]['regions'][region_id];
          if ((profile_data[task_id]['flags'] & 0x40) != 0) {
            document.getElementById('l2_value').innerHTML = my_region['l2_hit']; 
            document.getElementById('l3_value').innerHTML = my_region['l3_hit'];
            document.getElementById('ipc_value').innerHTML = my_region['ipc'];
          }
          else {
            document.getElementById('l2_value').innerHTML = "PCM disabled"; 
            document.getElementById('l3_value').innerHTML = "PCM disabled";
            document.getElementById('ipc_value').innerHTML = "PCM disabled";
          }
          document.getElementById('line_value').innerHTML = lineNum;
          document.getElementById('text_value').innerHTML = document.getElementById('line_'+lineNum.toString()).innerHTML.replace(lineNum.toString(),"").replace('&nbsp;',"");
          document.getElementById('task_value').innerHTML = task_id;
          document.getElementById('simd_value').innerHTML = profile_data[task_id]['total_num_lanes'];
          
          // Determine the lane usage

          var my_line = document.getElementById('wrapper_lane_0');
          //var my_line2 = document.getElementById('lane_0');
          var index = 0;
          console.log("DAFUQ", my_line);
          while(my_line != null) {
            console.log("WAAAAAAAAAAAT", my_line.parentNode, my_line.parentNode);
            
            my_line.parentNode.parentNode.removeChild(my_line.parentNode);
            //my_line.parentNode.removeChild(my_line);
            console.log(my_line);
            //console.log(my_line2);
            index++;
            my_line = document.getElementById('wrapper_lane_'+index.toString());
            //my_line2 = document.getElementById('lane_'+index.toString());
          }

          for (var i = 0; i < my_region['lane_usage'].length; i++) { 
            document.getElementById('stat_box').innerHTML += 
            "<tr id='wrapper_lane_" + i.toString()  + "'></tr> <td id='lane_"+i.toString()+"'><strong> Branch "+ i.toString() +
            " Lane Usage </strong></td><td>"+my_region['lane_usage'][i]['percent'].toString() +"</td></tr>";
          }

          console.log("line 255 in js"); 
          box.style.display = "block";
          return;
        }
      }
      // If we get here then it must be in between two regions and thus not in one itself
        console.log("HIDE DAMN IT");
        box.style.display = "none";
      return;
    }
  }
    console.log("HERE?");
    // It's either after the last region or in the last region
      if (profile_data[task_id]['regions'].length == 0) { 
        console.log("u jelly?");
        return;
        }
      var last_region = profile_data[task_id]['regions'].slice(-1)[0];
      if (last_region['end_line'] >= lineNum) {
          var my_region = last_region;

          if ((profile_data[task_id]['flags'] & 0x40) != 0) {
            console.log("FLAG SET");
            document.getElementById('l2_value').innerHTML = my_region['l2_hit']; 
            document.getElementById('l3_value').innerHTML = my_region['l3_hit'];
            document.getElementById('ipc_value').innerHTML = my_region['ipc'];
          }
          else {
            console.log("FLAG NOT SET");
            document.getElementById('l2_value').innerHTML = "PCM disabled"; 
            document.getElementById('l3_value').innerHTML = "PCM disabled";
            document.getElementById('ipc_value').innerHTML = "PCM disabled";
          }

          document.getElementById('line_value').innerHTML = lineNum;
          document.getElementById('text_value').innerHTML = document.getElementById('line_'+lineNum.toString()).innerHTML.replace(lineNum.toString(),"").replace('&nbsp;',"");
          document.getElementById('task_value').innerHTML = task_id;
         
          var my_line = document.getElementById('wrapper_lane_0');
          //var my_line2 = document.getElementById('lane_0');
          var index = 0;
          console.log("DAFUQ", my_line);
          while(my_line != null) {
            console.log("WAAAAAAAAAAAT", my_line.parentNode, my_line.parentNode);
            /*
            while(my_line2.hasChildNodes()) {
              console.log("DIEEEEEEE", my_line2.firstChild);
              my_line2.removeChild(my_line2.firstChild);
            } 
            */
            my_line.parentNode.parentNode.removeChild(my_line.parentNode);
            //my_line.parentNode.removeChild(my_line);
            console.log(my_line);
            //console.log(my_line2);
            index++;
            my_line = document.getElementById('wrapper_lane_'+index.toString());
            //my_line2 = document.getElementById('lane_'+index.toString());
          }
          for (var i = 0; i < my_region['lane_usage'].length; i++) { 
            document.getElementById('stat_box').innerHTML += 
            "<tr id='wrapper_lane_" + i.toString()  + "'></tr> <td id='lane_"+i.toString()+"'><strong> Branch "+ i.toString() +
            " Lane Usage </strong></td><td>"+my_region['lane_usage'][i]['percent'].toString() +"</td></tr>";
          }
          
          /*
          // Determine the lane usage
          if (my_region['lane_usage'].length == 1) {
          document.getElementById('lane_1').innerHTML = my_region['lane_usage'][0]['percent'];
          document.getElementById('lane_2').innerHTML = "N/A";
          }
          else if (my_region['lane_usage'].length == 2) { 
            document.getElementById('lane_1').innerHTML = my_region['lane_usage'][0]['percent']; 
            document.getElementById('lane_2').innerHTML = my_region['lane_usage'][1]['percent'];
          }
          else {
            document.getElementById('lane_1').innerHTML = "N/A";
            document.getElementById('lane_2').innerHTML = "N/A";
          }
          */
         console.log("line 346?"); 
          box.style.display = "block";
          return;
    }
    // It's in the last region
  console.log("HHHHH");
  box.style.display = "none";
}

function switch_task(new_task_id) { 
/*
    var previously_selected = document.getElementsByClassName("selected_tab ");
      while (previously_selected.length > 0) {
        previously_selected[0].className = 
          previously_selected[0].className.replace( /(?:^|\s)selected_tab(?!\S)/g , '' );
      }
  //document.getElementById('tab_' + new_task_id.toString()).class += 'selected_tab'; 
  //document.getElementById('tab_0').className += 'selected_tab';
  //console.log("CMON FIREWORKSSSSSSSSSSSSSSSSSSSS");
  //console.log(document.getElementById('tab_0'));
 */

  deselect_all_lines();
  document.getElementById('task_menu_wrapper').innerHTML = 'Task ' + new_task_id.toString(); 
  task_id = new_task_id;
  toggle_all();
}

function create_task_menu() {
  var html = '';
  for (var i = 0; i < profile_data.length; i++) {
    if (i != 0) {
      html += '<li class="divider"></li>' 
    } 
    html += '<li><a id=tab_' + i.toString() + ' onclick=switch_task('+i.toString()+')> Task ' + i.toString() + '</a></li>';
  }
  return html;
}

function create_tips() {
  var html = '<h1 class="text-center"> Suggested Optimizations<hr></h1>';
  html += '<h1 style="display:inline" title="For if statements, \'cif\' is preferred over \'if\' when all lanes go down the same branch"><small> Using Coherent Control Flow Constructs </small></h1>';

  // For each task
  for (var i = 0; i < complete_profile.length; i++) {
    var regions = complete_profile[i]['regions'];
    // For each region
    for (var j = 0; j < regions.length; j++) { 
      // Check if it's an if statement
      if ((regions[j].region_type == 0 || regions[j].region_type == 2)) {
        // Check the full_mask
        for (var k = 0; k < regions[j].full_mask_percentage.length; k++) {
          if (regions[j].full_mask_percentage[k].percent > 0.50) {
            html += '<li class="lead"> Line ' +  regions[j].full_mask_percentage[k].percent.toString() + ' in \"' + source_file  + '\": replace if with cif </li>'}
           //html += '<div style="width:50%" class=" alert alert-info" role="alert"> Line 100 ' + regions[j].full_mask_percentage[k].percent.toString() + '</div>';
           }
         } 
        }
      }
  return html;
}
