import os, re
os.system('grep --exclude=*php~ --exclude=*tmp --exclude=*svn* -r ">apply_filter" ../trunk/ > filters.txt')

pat = re.compile(""".*?['"](.*?)['"].*""")

print """
title: Filters

Filters are much like hooks, except these simply allow you to make minor and much simpler changes to the system, by allowing you to change a value, such as a URL, page name, and so on.


"""

def matchparen(string):
  level = 0
  count = 0
  newstr = ""
  for char in string:
    newstr += char
    if char == "(":
      count += 1
      level += 1
    elif char == ")":
      count += 1
      level -= 1
    if count > 0 and level == 0:
      return newstr
  return newstr

for line in open("filters.txt"):
  linepart = line.replace("../trunk/","").replace("\\","").split(":")
  code = linepart[1]
  code = matchparen(code[code.find("$api"):])
  #print "MAGIC ",code
  m = pat.match(code)
  if m:
    lp = code.strip().split("//")
    print "Topic: ",m.group(1)
    if len(lp) > 1:
      print lp[1]
    code = lp[0]
    print linepart[0].strip()
    print "(start code)"
    print code
    print "(end code)"
    print ""
