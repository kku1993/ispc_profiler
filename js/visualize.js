
  function search_line(lineNum) {
    for (var i = 0; i < profile_data[task_id]['regions'].length; i++) {
      // Found the region this click belongs to
      if (profile_data[task_id]['regions'][i]['start_line'] > lineNum || i == (profile_data[task_id]['regions'].length-1)) {
        // If it's prior to the first start_line then only highlight the line
        if (i == 0) {
          console.log(lineNum);
          document.getElementById('line_'+lineNum.toString()).className += "selected_line ";
          document.getElementById('line_'+lineNum.toString()).setAttribute('line_color', 'yellow');
          console.log("OWO");
          break;
        }
        // If contained within this region, highlight entire region
        if (lineNum <= profile_data[task_id]['regions'][i-1]['end_line']) {
          for (var j = profile_data[task_id]['regions'][i-1]['start_line']; j < (profile_data[task_id]['regions'][i-1]['end_line']+1); j++) {
            //console.log("JELLY")
              highlight_line(i-1,j);
            //console.log("END OF JELLY");
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
  //console.log("start",region_id," ",line_number);
  document.getElementById('line_'+line_number.toString()).className += "selected_line ";
  //console.log("wtf");

  if (profile_data[task_id]['regions'][region_id].lane_usage.length == 0) {
    //console.log('length too small');
    document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'yellow');
    return;
  }

  // Only one lane, TODO: Make more generic for multiple possible lanes
  if (profile_data[task_id]['regions'][region_id].lane_usage.length == 1) { 
    //if (profile_data[task_id]['regions'][region_id].lane_usage[0].line == profile_data[task_id]['regions'][region_id].start_line) { 
    if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 30) { 
      //console.log("hohoho", document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'black');

      //document.getElementById('line_'+line_number.toString()).line_color = 'black');
      //console.log('black');
      //console.log("rororo",document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
    }
    else if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 70) {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'green');
      console.log('green');
    }
    else {
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'red');
      console.log('red');
    }
  return;
  }

  // If youre fall in the first part of the lane usage
  if (line_number <= profile_data[task_id]['regions'][region_id].lane_usage[1].line) {
    if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 30) { 
      //console.log("hohoho", document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'black');

      //document.getElementById('line_'+line_number.toString()).line_color = 'black');
      //console.log('black');
      //console.log("rororo",document.getElementById('line_'+line_number.toString()).getAttribute('line_color'));
    }
    else if (profile_data[task_id]['regions'][region_id].lane_usage[0].percent < 70) {
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
    //console.log("ELSE HAAAAAAAAA");
    if (profile_data[task_id]['regions'][region_id].lane_usage[1].percent < 30) {       i
      console.log('black');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'black');
    }
    else if (profile_data[task_id]['regions'][region_id].lane_usage[1].percent < 70) {
      console.log('green');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'green');
    }
    else {
      console.log('red');
      document.getElementById('line_'+line_number.toString()).setAttribute('line_color', 'red');
    }
  }
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
    var source_code = document.querySelectorAll("[flag=source_code]"); 
      for (var i = 1; i < source_code.length+1; i++) {   
        search_line(i);
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
    // Found the region this click belongs to
    if (profile_data[task_id]['regions'][i]['start_line'] > lineNum || i == (profile_data[task_id]['regions'].length-1)) {
      // If it's prior to the first start_line then only highlight the line
      if (i == 0) {
        console.log("WHY U NO HIDE?");
        box.style.display = "none";
        return;
      }
      // If contained within this region, extract the stats for this region
      if (lineNum <= profile_data[task_id]['regions'][i-1]['end_line']) {
        document.getElementById('ipc_value').innerHTML = profile_data[task_id]['regions'][i-1]['ipc'];
        document.getElementById('l2_value').innerHTML = profile_data[task_id]['regions'][i-1]['l2_hit']; 
        document.getElementById('l3_value').innerHTML = profile_data[task_id]['regions'][i-1]['l3_hit'];
        document.getElementById('line_value').innerHTML = lineNum;
        document.getElementById('text_value').innerHTML = document.getElementById('line_'+lineNum.toString()).innerHTML; 
        document.getElementById('task_value').innerHTML = task_id; 
        box.style.display = "block";
        return;
        }
      // If not, it must not belong to any region TODO CHECK FOR LARGER REGION in both functions, after finding the last start_line possible, check every end line
      else {
        console.log("HIDE DAMN IT");
        box.style.display = "none";
      }
      return;
    }
  }
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
