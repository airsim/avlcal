<?php
 require ("connection.inc.php");

 $flightDate = "2001-10-18";

 $segmentListRequest = "SELECT * FROM "
          . "segmentdate, segmentcabin, cabin, "
          . "bookingclassdate, bookingclass, subclass "
          . "WHERE segmentdate.flightdateid='".$FlightDateID."' "
          . "AND segmentcabin.segmentdateid=segmentdate.segmentdateid "
          . "AND segmentcabin.cabinid=cabin.cabinid "
          . "AND bookingclassdate.segmentcabinid=segmentcabin.segmentcabinid "
          . "AND bookingclassdate.bookingclassid=bookingclass.bookingclassid "
          . "AND subclass.bookingclassdateid=bookingclassdate.bookingclassdateid";
 $segmentListResult = mysql_query ($segmentListRequest, $db);

?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Segments Details for <?php echo $flightDate." (Flight #".$FlightDateID.")"; ?></title>
</head>

<body bgcolor="#FFFFFF" vlink="#0000FF" alink="#0000FF">

<h1>Segments Details for <?php echo $flightDate." (Flight #".$FlightDateID.")"; ?></h1>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr>
  <td bgcolor="#DCDCDC">
   <table border="0" cellpadding="2" width="100%">
    <tr bgcolor="#EEEEEE">
     <!-- td>&nbsp;</td -->
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>SEG</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>CPT</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>CLASS</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>AVL CTL TYPE</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>BKGS</b>
      </font></th>
    </tr>

<?php
 while ($myrow = mysql_fetch_array ($segmentListResult)) {
?>

    <tr bgcolor="#FFFFFF">
     <td width="16%"><font size="-1"><?php echo $myrow["DepartureStation"].$myrow["ArrivalStation"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["Cabin"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["BookingClass"].$myrow["ClassOrder"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["AvailabilityControlType"]; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $myrow["BookingsCounter"]; ?>
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