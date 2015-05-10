#!/usr/bin/python

# Script to turn user source code into a format that can be highlighted by 
# highligh.js

import sys
import json
import random
from operator import itemgetter, attrgetter, methodcaller
if __name__ == '__main__':


  if (len(sys.argv) < 4):
    print "Usage: ./format_code.py [path to profile file] [task number] [start line]"
    #sys.exit(1) 

  profile_file = open(sys.argv[1], "r")
  profile = "".join([l for l in profile_file])
  profile_file.close()

  info = json.loads(profile)
  """
  info.regions.sortByStart = 
    function() {
      this.sort(function(a,b) {
        return a.start_line - b.start_line;
        });
      };
  """
  #print(info)
  info.sort(key=lambda x: x['task'])
  for j in xrange(len(info)): 
    info[j]['regions'].sort(key=lambda x: x['start_line'])
    for i in xrange(len(info[j]['regions'])):
      info[j]['regions'][i]['region_id'] = i
  with open('input2.txt', 'w') as outfile:
      json.dump(info, outfile)
  """
  target = info["task"+sys.argv[2]][sys.argv[3]]

  lane = ""
  for lane_index in xrange(len(target["lane"])):
    lane += " lane" + str(lane_index) + "=" + str(target["lane"][lane_index])
  
  hit = "hit=" + str(target["cache_hit"])
  miss = "miss=" + str(target["cache_miss"])
  block_type = "type=" + str(target["type"])


  print "<br>"
  print "<profile_line> PROFILE RESULTS </profile_line>" 
  print "<br>"
  print "<lane> %s </lane>" % (lane)
  print "<br>"
  print "<hit_line> %s </hit_line>" % (hit)
  print "<br>"
  print "<miss_line> %s </miss_line>" % (miss)
  """
