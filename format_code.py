#!/usr/bin/python

# Script to turn user source code into a format that can be highlighted by 
# highligh.js

import sys

if __name__ == '__main__':
  if (len(sys.argv) < 2):
    print "Usage: ./format_code.py [path to source code]"
    sys.exit(1) 

  code = open(sys.argv[1], "r")
  i = 1
  for line in code:
    # Remove trailing whitespaces
    line = line.rstrip()
    
    # Replace all "<" with &lt; so it can be properly displayed as a HTML page.
    line = line.replace("<", "&lt;")

    # Append and prepend a special html tag to the line so the user can 
    # highlight and interact with it.
    # TODO install custom onclick handler
    line = "<cline onclick='alert(" + str(i) + ")';>" + line + "</cline>"

    print line
    i += 1

  code.close()
