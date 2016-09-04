<?php
// Set the bookings counters on the leg-dates
function setLegBookingsCounters (&$flightDateArray, $flightDateID) {
  $currentFlightDate =& $flightDateArray[$flightDateID];
  foreach ($currentFlightDate->legDateArray as $legDate) {
    if (isset ($currentFlightDate->legDateArray[$legDate->legDateID])) {
      $currentFlightDate->legDateArray[$legDate->legDateID]->setBookingsCounters ($flightDateArray);
    }
  }
}

// Set the AvPools on the leg-dates
function setLegAvPools (&$flightDateArray, $flightDateID) {
  $currentFlightDate =& $flightDateArray[$flightDateID];
  foreach ($currentFlightDate->legDateArray as $legDate) {
    if (isset ($currentFlightDate->legDateArray[$legDate->legDateID])) {
      $currentFlightDate->legDateArray[$legDate->legDateID]->setAvPools ($flightDateArray);
    }
  }
}

?>
