<?php
/**
* A simple shortest app path finding script in PHP, for use in network or command line calls.
**/

$nodeA = (int)$_GET['a'];
$nodeB = (int)$_GET['b'];

header('Content-type: application/json');
echo json_encode(route($nodeA, $nodeB));

/*
 * Given two nodes of a graph return the shortest path between them
 */
function route($nodeA, $nodeB) {

	$data = db('SELECT a,b FROM network_connectivity', 'db.sqlite');

	// convert to an adjacency list, i.e. a map of node ids containing their neighbouring node ids
	$a_list = alist($data);

	// BASE SCENARIOS, cheap and quick initial checks
	// start or end not existing in data (!A or !B)
	if (!isset($a_list[$nodeA]) || !isset($a_list[$nodeB])) $result = [];

	// referring to self (A = B)
	elseif ($nodeA == $nodeB) $result = [$nodeA];

	// is a direct neighbour (A -> B)
	elseif (isset($a_list[$nodeA][$nodeB])) $result = [$nodeA, $nodeB];

	// find path chain between start and end (A -> ... <- B)
	else {

	     // if going from right (e.g. 5) to left (e.g. 1) (assumption that node ids are numerical)
	     if ($nodeA > $nodeB) {

	       $reverse = $nodeA;
	       $nodeA   = $nodeB;
	       $nodeB   = $reverse;
	     }

 	     $result = traverse($a_list, $nodeA, $nodeB);

	     // the path is always being discovered the same way, so if the request made was right to left the shortest path is still the same, only reversed
	     if (isset($reverse)) $result = array_reverse($result);
	}

        return $result;
}

/*
 * Helper function to transform db dataset to an adjacency list
 */
function alist(&$paths) {

        $list = [];

	foreach ($paths as $path) {

            // map node to neighbour, 1 is used as a distance value (unweighted / all nodes are treated as being equidistant)
   	    $list[$path['a']][$path['b']] = 1;

	    // map inverse path as well (bidirectional map instead of unidirectional)
   	    $list[$path['b']][$path['a']] = 1;
	}

	return $list;
}


/*
 * Helper function to traverse the graph and retrieve intermediate path between A and B
 */
function traverse(&$a_list, $nodeA, $nodeB, $intermediates = [])
{

     // find if A and B have common neighbours
     $neighboursA = array_keys($a_list[$nodeA]);
     $neighboursB = array_keys($a_list[$nodeB]);
     $common      = array_intersect($neighboursA, $neighboursB);
     array_unshift($intermediates, $nodeB); // keep tally of intermediates

     // if matching neighbour is found
     if ($common) {

	     // if more than one matching neighbour is found between A and B, it's still the same distance, so just getting the first of the lot via [0]
	     $result = array_merge([$nodeA, array_values($common)[0]], $intermediates);

	     return $result;
     }

     // else go deeper walking backwards from node B's neighbours till a common neighbour is found to node A's neighbours
     else {

          foreach ($neighboursB as $nvalue) {

 	      return traverse($a_list, $nodeA, $nvalue, $intermediates);
	  }
     }
}


/*
 * Helper function to query db (SQLIte)
 */
function db($sql, $dbfile)
{
   $db     = new SQLite3($dbfile);
   $result = $db->query($sql);
   $data   = [];

   while ($res = $result->fetchArray(1)) array_push($data, $res);

   return $data;
}
