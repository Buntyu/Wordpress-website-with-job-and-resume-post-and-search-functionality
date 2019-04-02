<?php
$host = "localhost";
$uname = "citysca1_db1";
$pass = "PLE+C.!N~)Um";
$database = "citysca1_db1";

$connection = mysql_connect($host, $uname, $pass);

echo mysql_error();

//or die("Database Connection Failed");
$selectdb = mysql_select_db($database) or
        die("Database could not be selected");
$result = mysql_select_db($database,$connection) or die("database cannot be selected <br>");

// Fetch Record from Database

$output = "";
$table = "EMPLOYERS"; // Enter Your Table Name 
$sql = mysql_query("select * from $table");


$columns_total = mysql_num_fields($sqls);

// Get The Field Name

for($i = 0; $i < $columns_total; $i++)
{
    $heading = mysql_field_name($sql, $i);
    $output .= '"' . $heading . '",';
}
$output .="\n";

// Get Records from the table

while($row = mysql_fetch_array($sql))
{
    for($i = 0; $i < $columns_total; $i++)
    {
        $output .='"' . $row["$i"] . '",';
    }
    $output .="\n";
}

// Download the file

$filename = "myFile.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

echo $output;
exit;
?>