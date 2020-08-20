# shortest-path
A simple shortest app path finding script in PHP, for use in network or command line calls.

SYSTEM REQUIREMENTS

These should normally exist by default on a dev machine:
* php-cli
* php-json
* php-sqlite

FILES

* index.php (code)
* db.sqlite (SQLITE database file with populated data)

HOW TO RUN

1) Via command line, within the same directory that the files reside, set the php built-in
server running, can be any port:

php -S localhost:8000

2) Query either via curl providing values for point A and point B (escape the ? and &amp;
symbols with a backslash if testing from command line), or via browser

curl 127.0.0.1:8000\?a=1\&amp;b=5 (command line)

or

127.0.0.1:8000?a=1&amp;b=5 (browser)

ASSUMPTIONS
* The paths can be generated from left to right, or right to left, e.g. curl
127.0.0.1:8000?a=1\&amp;b=5 will return [1,6,4,5] and curl 127.0.0.1:8000?b=5\&amp;b=1 will
return [5,4,6,1]

* As no distances have been defined between nodes, the graph is treated as an
unweighted graph, or all weights are equal.

* The data provided to the script via is expected to be representing a singular graph of
nodes, e.g. no isolated nodes or isolated networks of nodes apart from the main one.
