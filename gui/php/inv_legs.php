<?php
 require ("connection.inc.php");
 require ("class.inc.php");
 session_start ();
 require ("flight_functions.php");
 require ("leg_functions.php");
 require ("segment_functions.php");

 $currentFlightDate =& $flightDateArray[$FlightDateID];

 $currentFlightDetails = 
   $currentFlightDate->airlineDesignator.$currentFlightDate->flightNumber
   ." - ".$currentFlightDate->date;

 //***
 //* Calculate the availabilities
 //*

 // Set the bookings counters within the leg-dates/cabins
 setLegBookingsCounters ($flightDateArray, $FlightDateID);

 // Set the AvPools within the leg-dates/cabins
 setLegAvPools ($flightDateArray, $FlightDateID);

 // Set the availabilities within the segment-dates/cabins
 setSegmentAvailabilities ($currentFlightDate);


?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Legs Details for <?= $currentFlightDetails ?></title>
</head>

<body bgcolor="#FFFFFF" vlink="#0000FF" alink="#0000FF">

<center><h3>Legs Details for <?= $currentFlightDetails ?></h3></center>

<pre>
<?php
  // $segmentDate4 =& $currentFlightDate->segmentDateArray["4"];
  // $segmentRouting =& $segmentDate4->getSegmentRouting ($currentFlightDate);
  // print_r ($segmentRouting);
  // print_r ($segmentDate4);

  // $legDate2 =& $currentFlightDate->legDateArray["2"];
  // $crossedSegments =& $legDate2->getCrossedSegments ($flightDateArray);
  // print_r ($legDate2);
  // print_r ($crossedSegments);

  // $legCabin3 =& $currentFlightDate->legDateArray["2"]->legCabinArray["3"];
  // $crossedSegmentCabins =& $legCabin3->getCrossedSegmentCabins ($flightDateArray);
  // print_r ($legCabin3);
  // print_r ($crossedSegmentCabins);

  // print_r ($flightDateArray);

?>
</pre>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr>
  <td bgcolor="#DCDCDC">
   <table border="0" cellpadding="2" width="100%">
    <tr bgcolor="#EEEEEE">
     <!-- td>&nbsp;</td -->
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>LEG</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>CPT</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>CAP</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>ADJ</b>
      </font></th>
      <th align="left"><font face="Arial,Helvetica" size="-1">
       <b>BKGS</b>
      </font></th>
    </tr>

<?php
  foreach ($currentFlightDate->legDateArray as $legDate) {
    foreach ($legDate->legCabinArray as $legCabin) {
?>

    <tr bgcolor="#FFFFFF">
     <td width="16%"><font size="-1"><?php echo $legDate->departureStation.$legDate->arrivalStation; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $legCabin->cabin; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $legCabin->capacity; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $legCabin->adjustment; ?>
     </font></td>
     <td width="16%"><font size="-1"><?php echo $legCabin->bookingsCounter; ?>
     </font></td>
    </tr>

<?php
    }
  }
?>

   </table>
  </td>
 </tr>
</table>

<!-- Segment List -->
<table border="0" cellspacing="0" cellpadding="2" width="100%">
 <tr align="center">
  <td align="left"><font face="Arial" size="-1">
    <?php 
      echo "Codeshared flight-dates: | &nbsp; ";
      foreach ($flightDateArray as $otherFlightDate) {
        if ($otherFlightDate->flightDateID == $FlightDateID) {
          continue;
        }
        echo "<a href=\"inv.php?";
        echo "FlightDateID=".$otherFlightDate->flightDateID;
        echo "\" target=\"_top\">";
        echo $otherFlightDate->airlineDesignator.$otherFlightDate->flightNumber;
        echo "</a> &nbsp; | &nbsp; ";
      }
    ?>
   </font>
  </td>
  <td align="right"><font face="Arial" size="-1">
    <?php 
      echo "Segments: | &nbsp; ";
      foreach ($currentFlightDate->segmentDateArray as $segmentDate) {
        echo "<a href=\"segment_detail.php";
        echo "?FlightDateID=".$segmentDate->flightDateID;
        echo "&SegmentDateID=".$segmentDate->segmentDateID;
        echo "&DepartureStation=".$segmentDate->departureStation;
        echo "&ArrivalStation=".$segmentDate->arrivalStation;
        echo "\" target=\"segmentFrame\">";
        echo $segmentDate->departureStation.$segmentDate->arrivalStation;
        echo "</a> &nbsp; | &nbsp; ";
      }
    ?>
   </font>
  </td>
 </tr>
</table>

</body>
</html>