<?php
 require ("connection.inc.php");

 if (!$recordStep) {
   $recordStep = 30;
 }
 if (!$fromRecord) {
   $fromRecord = 0;
 }

if (!$totalCount) {
   $countRequest = "SELECT count(*) AS totalCount FROM flightdate "
      . " WHERE date='".$dateSelected."' ";
   $totalCountResult = mysql_query ($countRequest, $db);
   $totalCountRow = mysql_fetch_array ($totalCountResult);
   $totalCount = $totalCountRow["totalCount"];
 }

 $stepNumber = ceil ($totalCount / $recordStep);

?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Flight List for <?php echo $flightDate; ?></title>
</head>

<body bgcolor="#FFFFFF" vlink="#0000FF" alink="#0000FF">

<center><h3>Flight List for <?php echo $flightDate; ?></h3></center>

<center>
<table border="0" cellspacing="0" cellpadding="0" width="20%">
 <tr>
  <td bgcolor="#DCDCDC">
   <table border="0" cellpadding="2" width="100%">
    <tr bgcolor="#EEEEEE">
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b><a href="<?php echo basename($PHP_SELF)."?orderByLabel=ORDER+BY+FlightNumber&totalCount=$totalCount"; ?>"
             onmouseover="window.status='Sort by FlightNumber'; return true;"
             onmouseout="window.status=''; return true;">Flight Number</a></b>
      </font></th>
    </tr>

<?php
 $listRequest = "SELECT * FROM flightdate "
  	          . " WHERE date='".$dateSelected."' "
	          . $orderByLabel
                  . " LIMIT $fromRecord, " . $recordStep;
 $result = mysql_query ($listRequest, $db);
 while ($myrow = mysql_fetch_array ($result)) {
?>

    <tr bgcolor="#FFFFFF">
     <td align="center"><font size="-1">
      <a href="inv.php?FlightDateID=<?= $myrow["FlightDateID"] ?>"
         target="_top">
        <?= $myrow["AirlineDesignator"].$myrow["FlightNumber"] ?></a>
     </font></td>
    </tr>

<?php
 }
?>

   </table>
  </td>
 </tr>
</table>
</center>

</body>
</html>