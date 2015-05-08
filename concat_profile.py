#!/usr/bin/python

# Script to concatenate individual JSON objects for each profile context into 
# a JSON array.

import sys
import json
import random

if __name__ == '__main__':
  if (len(sys.argv) < 3):
    print "Usage: ./concat_profile.py [paths to profile files ...] [output path]"
    sys.exit(1) 

  obj = "[\n"
  for path in sys.argv[1 : len(sys.argv) - 1]:
    with open(path, "r") as profile:
      data = profile.read().rstrip()
      obj += data + ","

  # Remove last comma
  obj = obj[0 : len(obj) - 1] + "]"

  f = open(sys.argv[-1], "w")
  f.write(obj)
  f.close()
