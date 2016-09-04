<?php
 require ("connection.inc.php");
 require ("class.inc.php");
 session_register ("flightDateArray");
 require ("flight_functions.php");
 require ("segment_functions.php");

 $flightDateArray =& getRelatedFlights ($db, $FlightDateID);
 $currentFlightDate =& $flightDateArray[$FlightDateID];
 $currentFlightDetails = 
   $currentFlightDate->airlineDesignator.$currentFlightDate->flightNumber
   ." - ".$currentFlightDate->date;

?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>INV Details for <?= $currentFlightDetails ?></title>
</head>

<frameset rows="35%,80%" cols="*">
 <frame name="legFrame" 
        src="inv_legs.php?FlightDateID=<?=$FlightDateID.'&'.SID?>">
 <!-- frame name="legFrame" src="inv_legs.php" -->
 <frame name="segmentFrame" src="blank.php">
</frameset>
<noframes>Your browser is not frame enabled</noframes>
