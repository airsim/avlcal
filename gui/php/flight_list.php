<?php
 require ("connection.inc.php");

 $flightDetailPage = "flight_detail.php";
 $invLegPage = "inv_legs.php";
 $invSegmentPage = "inv_segments.php";
 $segmentListPage = "segment_list.php";
 $INVPage = "inv.php";

 $flightDate = "2001-10-18";
 $tablename = "flightdate";
 $whereLabel = " WHERE date='".$flightDate."' ";

 if (!$recordStep) {
   $recordStep = 30;
 }
 if (!$fromRecord) {
   $fromRecord = 0;
 }

if (!$totalCount) {
   $countRequest = "SELECT count(*) AS totalCount FROM " . $tablename;
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

<h1>Flight List for <?php echo $flightDate; ?></h1>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr>
  <td bgcolor="#DCDCDC">
   <table border="0" cellpadding="2" width="100%">
    <tr bgcolor="#EEEEEE">
     <!-- td>&nbsp;</td -->
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b><a href="<?php echo basename($PHP_SELF)."?orderByLabel=ORDER+BY+FlightNumber&totalCount=$totalCount"; ?>" 
             onmouseover="window.status='Sort by FlightNumber'; return true;"
             onmouseout="window.status=''; return true;">Flight Number</a></b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>INV for legs</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>INV for segments</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>Segments</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>INV</b>
      </font></th>
    </tr>

<?php
 $listRequest = "SELECT * FROM " . $tablename
	          . $orderByLabel
  	          . $whereLabel
                . " LIMIT $fromRecord, " . $recordStep;
 $result = mysql_query ($listRequest, $db);
 while ($myrow = mysql_fetch_array ($result)) {
?>

    <tr bgcolor="#FFFFFF">
     <td width="16%"><font size="-1"><?php echo $myrow["AirlineDesignator"].$myrow["FlightNumber"]; ?>
     </font></td>
     <td width="16%"><font size="-1">
      <?php printf ("<a href=\"%s?FlightDateID=%s\">View</a>\n",
                    $invLegPage, $myrow["FlightDateID"]); ?>
     </font></td>
     <td width="16%"><font size="-1">
      <?php printf ("<a href=\"%s?FlightDateID=%s\">View</a>\n",
                    $invSegmentPage, $myrow["FlightDateID"]); ?>
     </font></td>
     <td width="16%"><font size="-1">
      <?php printf ("<a href=\"%s?FlightDateID=%s\">List</a>\n",
                    $segmentListPage, $myrow["FlightDateID"]); ?>
     </font></td>
     <td width="16%"><font size="-1">
      <?php printf ("<a href=\"%s?FlightDateID=%s\">View</a>\n",
                    $INVPage, $myrow["FlightDateID"]); ?>
     </font></td>
    </tr>

<?php
 }
?>

   </table>
  </td>
 </tr>
</table>

</body>
</html>