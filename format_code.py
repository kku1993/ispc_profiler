#!/usr/bin/python

# Script to turn user source code into a format that can be highlighted by 
# highligh.js

import sys
import json
import random

if __name__ == '__main__':
  if (len(sys.argv) < 2):
    print "Usage: ./format_code.py [path to source code]"
    sys.exit(1) 

  code = open(sys.argv[1], "r")
  i = 1
  for line in code:
    # Remove trailing white spaces then pad for a consistent number of whitespaces
    line = '{message: <{fill}}'.format(message=line.rstrip(), fill='80')
    
    # Replace all "<" with &lt; so it can be properly displayed as a HTML page.
    line = line.replace("<", "&lt;")

    # Append and prepend a special html tag to the line so the user can 
    # highlight and interact with it.

    onclickHandler = "clickedLine(" + str(i) + ");"

    # TODO use real percentage
    lane_percentage = random.randint(0, 100)
    if (lane_percentage < 30): 
      line = "<cline onclick='%s'>%s</cline>" % (onclickHandler, line)
    elif (lane_percentage < 70): 
      line = "<dline onclick='%s'>%s</dline>" % (onclickHandler, line)
    else: 
      line = "<eline onclick='%s'>%s</eline>" % (onclickHandler, line)

    print line
    i += 1

  code.close()

