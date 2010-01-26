import os, re
os.system('grep --exclude=*php~ --exclude=*tmp --exclude=*svn* -r ">run_hook" ../trunk/ > hooks.txt')

pat = re.compile(""".*['"](.*?)['"].*""")

print """
title: Hooks

Here is a simple list of hook names and where they are located, soon there will be a more in depth updating of this list, such as including the parameters that are passed by these hooks, if any.


"""

for line in open("hooks.txt"):
  linepart = line.replace("../trunk/","").replace("\\","").split(":")
  m = pat.match(linepart[1])
  if m:
    lp = linepart[1].strip().split("//")
    print "Hook: ",m.group(1)
    if len(lp) > 1:
      print lp[1]
    print linepart[0].strip()
    print "(start code)"
    print lp[0]
    print "(end code)"
    print ""
