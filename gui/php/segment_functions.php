<?php

// Fill the Inventory objects for a given flight-date from the database
function fillInventoryObjects (&$db, $flightDateID) {
}

// Fill the segment-related objects from the database
function fillSegmentBasedObjects (&$db, $segmentDateID) {
  $subclassListRequest = "SELECT cabin.CabinID, cabin.Cabin, BkgClassOrder, "
    . "SubClassOrder, subclass.SubClassID, "
    . "bookingclassdate.BookingClassDateID, BookingClass, "
    . "AvailabilityControlType, SubClassOrder, BookingsCounter  "
    . "FROM segmentdate, segmentcabin, cabin, "
    . "bookingclassdate, bookingclass, subclass "
    . "WHERE segmentdate.segmentdateid='".$segmentDateID."' "
    . "AND segmentcabin.segmentdateid=segmentdate.segmentdateid "
    . "AND segmentcabin.cabinid=cabin.cabinid "
    . "AND bookingclassdate.segmentcabinid=segmentcabin.segmentcabinid "
    . "AND bookingclassdate.bookingclassid=bookingclass.bookingclassid "
    . "AND subclass.bookingclassdateid=bookingclassdate.bookingclassdateid "
    . "ORDER BY cabin.cabinid, bookingclassdate.bkgclassorder, "
    . "subclass.subclassorder ";
  $subclassListResult = mysql_query ($subclassListRequest, $db);
  
  // Put the subclasses into the $segmentClassHolderArray array
  $segmentClassHolderArray = array ();
  while ($subclassRow = mysql_fetch_array ($subclassListResult)) {
    $subclassOrder = 10000*$subclassRow["CabinID"]
      + 100*$subclassRow["BookingClassOrder"]
      + $subclassRow["SubClassOrder"];
    $segmentClassHolder = new SegmentClassHolder;
    $segmentClassHolder->totalSubClassOrder = $subclassOrder;
    $segmentClassHolder->subClassID = $subclassRow["SubClassID"];
    $segmentClassHolder->bookingClassDateID = $subclassRow["BookingClassDateID"];
    $segmentClassHolder->cabin = $subclassRow["Cabin"];
    $segmentClassHolder->bookingClass = $subclassRow["BookingClass"];
    $segmentClassHolder->subClassOrder = $subclassRow["SubClassOrder"];
    $segmentClassHolder->bookingsCounter = $subclassRow["BookingsCounter"];
     $segmentClassHolder->availabilityControlType = $subclassRow["AvailabilityControlType"];
     $segmentClassHolderArray[$subclassOrder] = $segmentClassHolder;
  }
  return $segmentClassHolderArray;
}



// Get all the attributes of the given segment
function getSegment ($db, $segmentDateID) {
  $segment = new SegmentDate;
  $segmentRequest = "SELECT * FROM segmentdate "
    . "WHERE segmentdate.segmentdateid='".$segmentDateID."'";
  $segmentResult = mysql_query ($segmentRequest, $db);
  while ($segmentRow = mysql_fetch_array ($segmentResult)) {
    $segment->segmentDateID = $segmentDateID;
    $segment->flightDateID = $segmentRow["FlightDateID"];
    $segment->date = $segmentRow["Date"];
    $segment->departureStation = $segmentRow["DepartureStation"];
    $segment->arrivalStation = $segmentRow["ArrivalStation"];
    $segment->codeShareType = $segmentRow["CodeShareType"];
    $segment->type = $segmentRow["Type"];
    $segment->saleable = $segmentRow["Saleable"];
  }
  return $segment;
}

// Get the routing (in terms of legs) of the given segment
function getSegmentRouting ($db, $segment) {
  $legListRequest = "SELECT * "
    . "FROM segmentdate, flightdate, flightdaterouting, legdate "
    . "WHERE segmentdate.segmentdateid='".$segment->segmentDateID."' "
    . "AND flightdate.flightdateid=segmentdate.flightdateid "
    . "AND flightdaterouting.flightdateid=flightdate.flightdateid "
    . "AND legdate.legdateid=flightdaterouting.legdateid "
    . "ORDER BY flightdaterouting.legorder";
  $legListResult = mysql_query ($legListRequest, $db);

  // Put the leg-dates into the $legDateArray array
  $legDateArray = array ();
  $segmentDepartureStation = $segment->departureStation;
  $segmentArrivalStation = $segment->arrivalStation;
  $departureStationIndex = $segmentDepartureStation;
  while ($legRow = mysql_fetch_array ($legListResult)) {
    if ($legRow["DepartureStation"] != $departureStationIndex) {
      continue;
    }
    // Fill the legDate
    $legDate = new LegDate ($db, $legRow["LegDateID"]);
    $legDate->departureStation = $legRow["DepartureStation"];
    $legDate->arrivalStation = $legRow["ArrivalStation"];
    // Add the legDate to the legDate array (holding the routing)
     $legDateArray[$legRow["LegOrder"]] = $legDate;
     
     $departureStationIndex = $legRow["ArrivalStation"];
     if ($departureStationIndex == $segmentArrivalStation)
       break;
  }
  return $legDateArray;
}

// Set the routing (in terms of legs) of all the segments of a given flight
function setSegmentRouting ($db, &$flightDate) {
  
  foreach ($flightDate->segmentDateArray as $segmentDate) {
     
  }

  return $legDateArray;
}

function updateSegmentCabinCommittedSpace ($db, $segmentCabin) {
  $totalBookingsCounter = 0.0;
  $totalUPR = 0.0;
}

function setSegmentAvailabilities (&$flightDate) {
  if (!isset($flightDate->segmentDateArray)) {
    return;
  }
  foreach ($flightDate->segmentDateArray as $segmentDate) {
    if (!isset($segmentDate->segmentCabinArray)) {
      return;
    }
    foreach ($segmentDate->segmentCabinArray as $segmentCabin) {
      $flightDate->segmentDateArray[$segmentDate->segmentDateID]->segmentCabinArray[$segmentCabin->cabinID]->setAvailabilities ($flightDate);
    }
  }
}


?>
