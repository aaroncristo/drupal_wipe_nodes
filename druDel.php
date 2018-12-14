
<?php
#####################################################################################
/***
 * This file has to be executed using 'drush scr <script name>'.
 * Ensure this file is inside Drupal public root.
*/
#####################################################################################
//List of node types to delete - To add more tables comma separating each in the list below.
$node_type_to_del = ['node_type1','node_type2'];

//Database to fetch nodes
$f_db_host 	= 'localhost'; // or IP
$f_db_user 	= 'admin';
$f_db_name	= 'admin_db';
$f_db_pass	= 'admin_pass';

//Settings node range for deletion - Uncommenting the below fields would enable the range feature.
#$start 	= 2000;
#$end 		= 6000;

####################################################################################
//Confirmation.
echo 'Are you sure to procees(yes/no)? ';
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'yes'){
    echo "ABORTING!\n";
    exit;
}

//Populating nodes to an array.
$nodes = [];
//Deletion by range - enabled by uncommenting 
if(!empty($start) && !empty($end)) {
	$nodes = [];
	for($i=$start; $i<=$end ;$i++)
		$nodes[]= $i;
}
//Fetching from database.........
else {
	$con            = mysqli_connect($f_db_host,$f_db_user,$f_db_pass,$f_db_name);
	if(mysqli_connect_errno())
	        die('Could not connect: ' . mysqli_connect_errno()."\n");
	else {
	        echo "Connected to database ... \n";
	        $nodes = mysqli_query($con, "select nid from node where type IN ('".implode("','",$node_type_to_del)."');");
        	$rows = [];
        	while($row = mysqli_fetch_array($nodes))
                	$rows[] = $row['nid'];
	}
	$nodes = $rows;
}
//Entity deletion starts here.
$storageHandler = \Drupal::entityTypeManager()->getStorage("node");
$nodeEntity 	= $storageHandler->loadMultiple($nodes); 
$storageHandler->delete($nodeEntity);

?>
