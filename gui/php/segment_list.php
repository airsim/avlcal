<?php
 require ("connection.inc.php");

 $segmentDetailPage = "segment_detail.php";

 $listRequest = "SELECT * FROM segmentdate "
	        . $orderByLabel
	        . " WHERE flightDateID='".$FlightDateID."' ";
 // echo $listRequest;
 $result = mysql_query ($listRequest, $db);

?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Segment List for <?php echo $flightDate; ?></title>
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
       <b><a href="<?php echo basename($PHP_SELF)."?orderByLabel=ORDER+BY+DepartureStation&totalCount=$totalCount"; ?>" 
           onmouseover="window.status='Sort by Origin'; return true;"
           onmouseout="window.status=''; return true;">Origin</a></b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b><a href="<?php echo basename($PHP_SELF)."?orderByLabel=ORDER+BY+ArrivalStation&totalCount=$totalCount"; ?>" 
           onmouseover="window.status='Sort by Destination'; return true;"
           onmouseout="window.status=''; return true;">Destination</a></b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>CodeShare Type</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>Details</b>
      </font></th>
    </tr>

<?php
 while ($myrow = mysql_fetch_array ($result)) {
?>

    <tr bgcolor="#FFFFFF">
     <td width="16%"><font size="-1"><?php echo $myrow["DepartureStation"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["ArrivalStation"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["CodeShareType"]; ?>
     </font></td>
     <td width="16%"><font size="-1">
      <?php printf ("<a href=\"%s?SegmentDateID=%s&DepartureStation=%s&ArrivalStation=%s\">View</a>\n",
       $segmentDetailPage, $myrow["SegmentDateID"], $myrow["DepartureStation"], $myrow["ArrivalStation"]); ?>
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