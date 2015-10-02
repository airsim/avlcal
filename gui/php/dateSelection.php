<?php
 require ("connection.inc.php");
?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Date Selection Page</title>
</head>

<body bgcolor="#FFFFFF" vlink="#0000FF" alink="#0000FF">

<center><h3>Date Selection</h3></center>

<center>
<form name="dateForm" method="post" action="flightdate_list.php"
      target="flightFrame">
  <select name="dateSelected">
<?php
 $dateListRequest = "SELECT DISTINCT Date FROM flightdate ORDER BY Date";
 $dateListResult = mysql_query ($dateListRequest, $db);
 while ($dateRow = mysql_fetch_array ($dateListResult)) {
   echo "<option value=\"".$dateRow["Date"]."\">".$dateRow["Date"]."</option>\n";
 }
?>
  </select>

  <input type="submit" value="Go">
</form>
</center>

</body>
</html>