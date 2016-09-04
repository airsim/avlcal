<?php
 // Build and return all the flight-dates that have a codeshare relationship
 // with a given one. This given flight-date is built as well.
 function &getRelatedFlights (&$db, $flightDateID) {
   $relatedFlightArray = array ();

   // Get all the associated leg-dates
   $relatedLegIDArray = array ();
   $legIDListRequest = "SELECT DISTINCT LegDateID FROM flightdaterouting "
           . "WHERE flightdaterouting.flightdateid='".$flightDateID."' ";
   $legIDListResult = mysql_query ($legIDListRequest, $db);

   while ($legIDRow = mysql_fetch_array ($legIDListResult)) {
     $relatedLegIDArray[] = $legIDRow["LegDateID"];
   }

   // Get the related flight-date IDs from the FlightDateRouting table
   $relatedFlightIDArray = array ();
   foreach ($relatedLegIDArray as $legDateID) {
     $flightIDListRequest = "SELECT DISTINCT FlightDateID "
                       . "FROM flightdaterouting "
                       . "WHERE flightdaterouting.legdateid='".$legDateID."' ";
     $flightIDListResult = mysql_query ($flightIDListRequest, $db);

     while ($flightIDRow = mysql_fetch_array ($flightIDListResult)) {
       $tmpFlightDateID = $flightIDRow["FlightDateID"];
       if (!isset($relatedFlightIDArray[$tmpFlightDateID])) {
         $relatedFlightIDArray[$tmpFlightDateID] = $tmpFlightDateID;
       }
     }
   }

   foreach ($relatedFlightIDArray as $tmpFlightDateID) {
     $relatedFlightArray[$tmpFlightDateID] =& 
                                        new FlightDate ($db, $tmpFlightDateID);
   }
   return $relatedFlightArray;
 }
?>
