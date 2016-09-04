<?php

 class FlightDate {
   var $flightDateID;
   var $airlineDesignator;
   var $flightNumber;
   var $date;
   var $legDateArray;
   var $segmentDateArray;

   function FlightDate (&$db, $flightDateID) {
     $this->flightDateID = $flightDateID;

     $flightDateRequest = "SELECT AirlineDesignator, FlightNumber, Date "
                        . "FROM flightdate "
                        . "WHERE flightdate.flightdateid='".$flightDateID."' ";
     $flightDateResult = mysql_query ($flightDateRequest, $db);

     while ($flightDateRow = mysql_fetch_array ($flightDateResult)) {
       $this->airlineDesignator = $flightDateRow["AirlineDesignator"];
       $this->flightNumber = $flightDateRow["FlightNumber"];
       $this->date = $flightDateRow["Date"];
     }

     // Link to all the associated leg-dates
     $this->legDateArray = array ();
     $legListRequest = "SELECT legdate.LegDateID "
                     . "FROM flightdaterouting, legdate "
                     . "WHERE flightdaterouting.flightdateid='".$flightDateID."' "
                     . "AND legdate.legdateid=flightdaterouting.legdateid "
                     . "ORDER BY flightdaterouting.legorder ";
     $legListResult = mysql_query ($legListRequest, $db);

     while ($legRow = mysql_fetch_array ($legListResult)) {
       $this->legDateArray[$legRow["LegDateID"]] =& 
                        new LegDate ($db, $flightDateID, $legRow["LegDateID"]);
     }

     // Link to all the associated segment-dates
     $this->segmentDateArray = array ();
     $segmentListRequest = "SELECT SegmentDateID FROM segmentdate "
                       . "WHERE segmentdate.flightdateid='".$flightDateID."' ";
     $segmentListResult = mysql_query ($segmentListRequest, $db);

     while ($segmentRow = mysql_fetch_array ($segmentListResult)) {
       $this->segmentDateArray[$segmentRow["SegmentDateID"]] =& 
                           new SegmentDate ($db, $segmentRow["SegmentDateID"]);
     }
   }
 }

 class LegDate {
   // Aircraft part
   var $aircraftID;
   var $aircraftType;
   var $description;

   var $flightDateID;
   var $legDateID;
   var $operatingFlightDesignator;
   var $date;
   var $departureStation;
   var $arrivalStation;
   var $legCabinArray;

   function LegDate (&$db, $flightDateID, $legDateID) {
     $this->flightDateID = $flightDateID;
     $this->legDateID = $legDateID;

     $legDateRequest = "SELECT * FROM legdate, aircraft "
                     . "WHERE legdate.legdateid='".$legDateID."' "
                     . "AND aircraft.aircraftid=legdate.aircraftid ";
     $legDateResult = mysql_query ($legDateRequest, $db);

     while ($legDateRow = mysql_fetch_array ($legDateResult)) {
       $this->aircraftID = $legDateRow["AircraftID"];
       $this->aircraftType = $legDateRow["AircraftType"];
       $this->description = $legDateRow["Description"];
       $this->date = $legDateRow["Date"];
       $this->operatingFlightDesignator = $legDateRow["OperatingFlightDesignator"];
       $this->departureStation = $legDateRow["DepartureStation"];
       $this->arrivalStation = $legDateRow["ArrivalStation"];
     }

     // Get the associated leg-cabins
     $legCabinListRequest = "SELECT LegCabinID, CabinID FROM legcabin "
                                . "WHERE legcabin.legdateid='".$legDateID."' ";
     $legCabinListResult = mysql_query ($legCabinListRequest, $db);

     $this->legCabinArray = array ();
     while ($legCabinRow = mysql_fetch_array ($legCabinListResult)) {
       // Add the leg-date to the legDate array
       $this->legCabinArray[$legCabinRow["CabinID"]] =& 
                 new LegCabin ($db, $flightDateID, $legCabinRow["LegCabinID"]);
     }
   }

   function &getCrossedSegments (&$flightDateArray) {
     $crossedSegments = array ();
     foreach ($flightDateArray as $flightDate) {
       foreach ($flightDate->segmentDateArray as $segmentDate) {
         $segmentRoutingArray =& $segmentDate->getSegmentRouting ($flightDate);
         if (isset($segmentRoutingArray[$this->legDateID])) {
           $segmentDateID = $segmentDate->segmentDateID;
           $crossedSegments[$segmentDateID] =& 
                                 $flightDate->segmentDateArray[$segmentDateID];
         }
       }
     }
     return $crossedSegments;
   }

   // Set the bookings counters on all the leg-cabins of the leg-date
   function setBookingsCounters (&$flightDateArray) {
     foreach ($this->legCabinArray as $legCabin) {
       $flightDateArray[$this->flightDateID]->legDateArray[$this->legDateID]->legCabinArray[$legCabin->cabinID]->setBookingsCounter ($flightDateArray);
     }
   }

   // Set the AvPools on all the leg-cabins of the leg-date
   function setAvPools (&$flightDateArray) {
     foreach ($this->legCabinArray as $legCabin) {
       $flightDateArray[$this->flightDateID]->legDateArray[$this->legDateID]->legCabinArray[$legCabin->cabinID]->setAvPool ($flightDateArray);
     }
   }

 }

 class LegCabin {
   var $flightDateID;
   var $cabin;
   var $cabinID;
   var $legDateID;
   var $crossedSegmentArray;
   var $marketingFlightDesignator;
   var $capacity;
   var $adjustment;
   var $notificationLevel;
   var $bookingsCounter;
   var $operationalCapacity;
   var $netAvailability;
   var $grossAvailability;
   var $bidPrice;
   var $avPool;

   function LegCabin (&$db, $flightDateID, $legCabinID) {
     $this->flightDateID = $flightDateID;
     $this->legCabinID = $legCabinID;
     $this->crossedSegmentArray = array ();

     $legCabinRequest = "SELECT * FROM legcabin, cabin "
                      . "WHERE legcabin.legcabinid='".$legCabinID."' "
                      . "AND cabin.cabinid=legcabin.cabinid ";
     $legCabinResult = mysql_query ($legCabinRequest, $db);

     while ($legCabinRow = mysql_fetch_array ($legCabinResult)) {
       $this->legDateID = $legCabinRow["LegDateID"];
       $this->cabinID = $legCabinRow["CabinID"];
       $this->cabin = $legCabinRow["Cabin"];
       $this->marketingFlightDesignator = $legCabinRow["Marketingflightdesignator"];
       $this->capacity = $legCabinRow["Capacity"];
       $this->adjustment = $legCabinRow["Adjustment"];
       $this->notificationLevel = $legCabinRow["NotificationLevel"];
       $this->operationalCapacity = $legCabinRow["OperationalCapacity"];
       $this->netAvailability = $legCabinRow["NetAvailability"];
       $this->grossAvailability = $legCabinRow["GrossAvailability"];
       $this->bidPrice = $legCabinRow["BidPrice"];
       $this->avPool = $legCabinRow["AvPool"];
     }
   }

   // Get all the segment-cabins crossed by this leg-cabin
   function &getCrossedSegmentCabins (&$flightDateArray) {
     $crossedSegmentCabins = array ();

     $crossedSegments =& $flightDateArray[$this->flightDateID]->legDateArray[$this->legDateID]->getCrossedSegments ($flightDateArray);
     foreach ($crossedSegments as $segmentDate) {
       $crossedSegmentCabins[$segmentDate->segmentDateID] =& $segmentDate->segmentCabinArray[$this->cabinID];
     }
     return $crossedSegmentCabins;
   }

   // Set the bookings counter on the leg-cabin
   function setBookingsCounter (&$flightDateArray) {
     $this->bookingsCounter = 0.0;
     $crossedSegmentCabins=& $this->getCrossedSegmentCabins ($flightDateArray);

     foreach ($crossedSegmentCabins as $segmentCabin) {
       $this->bookingsCounter += $segmentCabin->bookingsCounter;
     }
   }

   // Set the AvPool on the leg-cabin
   function setAvPool (&$flightDateArray) {
     $this->avPool = $this->operationalCapacity;
     $crossedSegmentCabins=& $this->getCrossedSegmentCabins ($flightDateArray);

     foreach ($crossedSegmentCabins as $segmentCabin) {
       $this->avPool -= $segmentCabin->committedSpace;
     }
   }
 }

 class LegHolder {
   var $cabin;
   var $flightDateID;
   var $legDateID;
   var $date;
   var $departureStation;
   var $arrivalStation;
   var $marketingFlightDesignator;
   var $capacity;
   var $adjustment;
   var $notificationLevel;
   var $bookingsCounter;
   var $operationalCapacity;
   var $netAvailability;
   var $grossAvailability;
   var $bidPrice;
   var $avPool;
 }

 class SegmentDate {
   var $segmentID;
   var $flightDateID;
   var $date;
   var $departureStation;
   var $arrivalStation;
   var $codeShareType;
   var $type;
   var $saleable;
   var $segmentCabinArray;

   function SegmentDate (&$db, $segmentDateID) {
     $this->segmentDateID = $segmentDateID;
     $this->segmentCabinArray = array ();

     $segmentDateRequest = "SELECT * FROM segmentdate "
                     . "WHERE segmentdate.segmentdateid='".$segmentDateID."' ";
     $segmentDateResult = mysql_query ($segmentDateRequest, $db);

     while ($segmentDateRow = mysql_fetch_array ($segmentDateResult)) {
       $this->flightDateID = $segmentDateRow["FlightDateID"];
       $this->date = $segmentDateRow["Date"];
       $this->departureStation = $segmentDateRow["DepartureStation"];
       $this->arrivalStation = $segmentDateRow["ArrivalStation"];
       $this->codeShareType = $segmentDateRow["CodeShareType"];
       $this->type = $segmentDateRow["Type"];
       $this->saleable = $segmentDateRow["Saleable"];
     }

     // Get the associated segment-cabins
     $segmentCabinListRequest = "SELECT SegmentCabinID, CabinID "
                    . "FROM segmentcabin "
                    . "WHERE segmentcabin.segmentdateid='".$segmentDateID."' ";
     $segmentCabinListResult = mysql_query ($segmentCabinListRequest, $db);

     $this->segmentCabinArray = array ();
     while ($segmentCabinRow = mysql_fetch_array ($segmentCabinListResult)) {
       // Add the segment-cabin to the segmentCabin array
       $cabinID = $segmentCabinRow["CabinID"];
       $this->segmentCabinArray[$cabinID] =& 
     new SegmentCabin ($db, $flightDateID, $segmentDateID, 
                       $segmentCabinRow["SegmentCabinID"]);
       // Fill the nests
       $this->segmentCabinArray[$cabinID]->setNests ($db);
       $this->segmentCabinArray[$cabinID]->setUPRs ();
       $this->segmentCabinArray[$cabinID]->setCommittedSpaces ();
     }
   }

   // Get the segment routing (in terms of leg-dates)
   function &getSegmentRouting (&$flightDate) {
     $segmentRouting = array ();
     $departureStationIndex = $this->departureStation;
     $segmentArrivalStation = $this->arrivalStation;
     foreach ($flightDate->legDateArray as $legDate) {
       if ($legDate->departureStation != $departureStationIndex) {
           continue;
       }
       // Add the legDate reference to the segmentRouting array
       $legDateID = $legDate->legDateID;
       $segmentRouting[$legDateID] =& $flightDate->legDateArray[$legDateID];

       $departureStationIndex = $legDate->arrivalStation;
       if ($departureStationIndex == $segmentArrivalStation)
         break;
     }
     return $segmentRouting;
   }
 }

 class SegmentCabin {
   var $flightDateID;
   var $cabinID;
   var $cabin;
   var $segmentDateID;
   var $waitlistCounter;
   var $waitlistMax;
   var $bookingClassArray;
   var $displayNest;
   var $minNest;
   var $maxNest;

   // Intermediary variables for availability calculation
   var $bookingsCounter;
   var $UPR;
   var $committedSpace;
   var $availability;

   function SegmentCabin (&$db, $flighDateID, $segmentDateID, $segmentCabinID){
     $this->flightDateID = $flighDateID;
     $this->segmentDateID = $segmentDateID;
     $this->segmentCabinID = $segmentCabinID;
     $this->bookingsCounter = 0.0;

     $segmentCabinRequest = "SELECT cabin.CabinID, cabin.Cabin, "
                   . "WaitListCounter, WaitListMax "
                   . "FROM segmentcabin, cabin "
                   . "WHERE segmentcabin.segmentcabinid='".$segmentCabinID."' "
                   . "AND cabin.cabinid=segmentcabin.cabinid ";
     $segmentCabinResult = mysql_query ($segmentCabinRequest, $db);

     while ($segmentCabinRow = mysql_fetch_array ($segmentCabinResult)) {
       $this->cabinID = $segmentCabinRow["CabinID"];
       $this->cabin = $segmentCabinRow["Cabin"];
       $this->waitlistCounter = $segmentCabinRow["WaitlistCounter"];
       $this->waitlistMax = $segmentCabinRow["WaitlistMax"];
     }

     // Get the associated booking classes
     $bookingClassListRequest = "SELECT BookingClassDateID "
              . "FROM bookingclassdate "
              . "WHERE bookingclassdate.segmentcabinid='".$segmentCabinID."' "
              . "ORDER BY bookingclassdate.bookingclassorder ";
     $bookingClassListResult = mysql_query ($bookingClassListRequest, $db);

     $this->bookingClassArray = array ();
     while ($bookingClassRow = mysql_fetch_array ($bookingClassListResult)) {
       // Add the booking classes to the array
       $bookingClassDateID = $bookingClassRow["BookingClassDateID"];
       $this->bookingClassArray[$bookingClassDateID] =& 
                               new BookingClassDate ($db, $bookingClassDateID);
       $this->bookingsCounter += 
                $this->bookingClassArray[$bookingClassDateID]->bookingsCounter;
     }
   }

   // Set the Nest (MIN and MAX) values
   // Note: There can be several distinct nests of the same type (MIN or MAX)
   // within the same cabin. However, it is assumed these nests do not overlap
   // (i.e., a subclass can not be nested in two distinct nests of the same
   // type). Nevertheless, a MIN and a MAX nests can overlap: a subclass can
   // belong at the same time to both a MIN and a MAX nests.
   function setNests (&$db) {
     $this->displayNest = array ();
     $this->minNest = array ();
     $this->maxNest = array ();
     // Browse the sub-classes to build the MIN and MAX nests
     foreach ($this->bookingClassArray as $bookingClass) {
       foreach ($bookingClass->subclassArray as $subclass) {
         $subclassRef =& $this->bookingClassArray[$bookingClass->bookingClassDateID]->subclassArray[$subclass->subClassID];
         $subclassOrder = 10000*$this->cabinID
                        + 100*$bookingClass->bookingClassOrder
                        + $subclass->subClassOrder;
         if (!isset ($this->displayNest[$subclassOrder])) {
           $this->displayNest[$subclassOrder] = array ();
         }
         $nestListRequest = "SELECT ControlValue, subclassnest.SubClassNestID, ControlType "
        . "FROM subclassnestmapping, subclassnest "
        . "WHERE subclassnestmapping.subclassid='".$subclassRef->subClassID."' "
        . "AND subclassnest.subclassnestid=subclassnestmapping.subclassnestid";
         $nestListResult = mysql_query ($nestListRequest, $db);

         // Browse the nested values (MIN or MAX) for a given subclass
         while ($nestRow = mysql_fetch_array ($nestListResult)) {
           $nestType = $nestRow["ControlType"];
           $nestID = $nestRow["SubClassNestID"];
           $nestValue = $nestRow["ControlValue"];
           $this->displayNest[$subclassOrder][$nestType] = 
                                                $nestValue." ".chr(64+$nestID);
           if ($nestType == "MIN") {
             if (!isset ($this->minNest[$nestID])) {
               $this->minNest[$nestID] = array ();
             }
             $minValue = array ();
             $minValue["BookingClassDateID"] = $bookingClass->bookingClassDateID;
             $minValue["SubClassID"] = $subclass->subClassID;

             $this->minNest[$nestID][$subclassOrder] = $minValue;
             // Set the corresponding nested values within 
             // the concerned subclass
             $subclassRef->minNestValue = $nestValue;
             $subclassRef->minNestID = $nestID;
             //$subclassRef->displayMIN = $this->displayNest[$subclassOrder]["MIN"];
           }
           if ($nestType == "MAX") {
             if (!isset ($this->maxNest[$nestID])) {
               $this->maxNest[$nestID] = array ();
             }
             $maxValue = array ();
             $maxValue["BookingClassDateID"] = $bookingClass->bookingClassDateID;
             $maxValue["SubClassID"] = $subclass->subClassID;
             $this->maxNest[$nestID][$subclassOrder] = $maxValue;
             // Set the corresponding nested values within 
             // the concerned subclass
             $subclassRef->maxNestValue = $nestValue;
             $subclassRef->maxNestID = $nestID;
             //$subclassRef->displayMAX = $this->displayNest[$subclassOrder]["MAX"];
           }
         }

       }
     }

     // Set the cumulative bookings counters for the MIN nests
     foreach ($this->minNest as $nestID => $minNestArray) {
       // Reversely sort the array along the key ($subclassOrder)
       krsort ($minNestArray);
       $cumulativeBookingsCounter = 0.0;
       foreach ($minNestArray as $subclassOrder => $minNestValue) {
         $bookingClassDateID = $minNestValue["BookingClassDateID"];
         $subclassID = $minNestValue["SubClassID"];
         $subclass =& $this->bookingClassArray[$bookingClassDateID]->subclassArray[$subclassID];
         $cumulativeBookingsCounter += $subclass->bookingsCounter;
         $subclass->minCumulativeBookingsCounter = $cumulativeBookingsCounter;
       }
     }
     // Set the cumulative bookings counters for the MAX nests
     foreach ($this->maxNest as $nestID => $maxNestArray) {
       // Reversely sort the array along the key ($subclassOrder)
       krsort ($maxNestArray);
       $cumulativeBookingsCounter = 0.0;
       foreach ($maxNestArray as $subclassOrder => $maxNestValue) {
         $bookingClassDateID = $maxNestValue["BookingClassDateID"];
         $subclassID = $maxNestValue["SubClassID"];
         $subclass =& $this->bookingClassArray[$bookingClassDateID]->subclassArray[$subclassID];
         $cumulativeBookingsCounter += $subclass->bookingsCounter;
         $subclass->maxCumulativeBookingsCounter = $cumulativeBookingsCounter;
       }
     }
   }

   // Set the (cumulative) UPR values
   function setUPRs () {
     $this->UPR = 0.0;

     foreach ($this->minNest as $minNestArray) {
       $lastCumulativeUPR = 10000;
       $counter = 0;
       foreach ($minNestArray as $subclassOrder => $minNestValue) {
         $bookingClassDateID = $minNestValue["BookingClassDateID"];
         $subclassID = $minNestValue["SubClassID"];
         $subclass =& $this->bookingClassArray[$bookingClassDateID]->subclassArray[$subclassID];
         $subclass->UPR = 
           min ($subclass->minNestValue - $subclass->minCumulativeBookingsCounter,
                $lastCumulativeUPR);
         $subclass->UPR = max ($subclass->UPR, 0.0);
         $lastCumulativeUPR = $subclass->UPR;
         if ($counter == 0) {
           $this->UPR += $subclass->UPR;
         }
         $counter++;
       }
     }
   }

   // Set the Committed Space values
   function setCommittedSpaces () {
     $this->committedSpace = $this->UPR;

     foreach ($this->bookingClassArray as $bookingClass) {
       foreach ($bookingClass->subclassArray as $subclass) {
         $this->committedSpace += $subclass->bookingsCounter;
       }
     }
   }

   // Set the Availabilities
   function setAvailabilities (&$flightDate) {
     // Get the AvPool from the legs
     $currentSegmentDate=& $flightDate->segmentDateArray[$this->segmentDateID];
     $segmentRouting =& $currentSegmentDate->getSegmentRouting ($flightDate);
     $avPool = 10000;
     foreach ($segmentRouting as $legDate) {
       // The segment cabin is mapped onto the same leg cabin (it is assumed
       // they are the same)
       $legCabin =& $legDate->legCabinArray[$this->cabinID];
       $avPool = min ($legCabin->avPool, $avPool);
     }

     // MIN Nests processing
     foreach ($this->minNest as $minNestArray) {
       $lastMinValue = 10000;
       foreach ($minNestArray as $subclassOrder => $minNestValue) {
         $bookingClassDateID = $minNestValue["BookingClassDateID"];
         $subclassID = $minNestValue["SubClassID"];
         $subclass =& $this->bookingClassArray[$bookingClassDateID]->subclassArray[$subclassID];
         $subclass->availability = 
           min ($avPool + $subclass->UPR, $lastMinValue);
         $lastMinValue = $subclass->availability;
       }
     }
     // MAX Nests processing
     foreach ($this->maxNest as $maxNestArray) {
       $lastMaxValue = 10000;
       foreach ($maxNestArray as $subclassOrder => $maxNestValue) {
         $bookingClassDateID = $maxNestValue["BookingClassDateID"];
         $subclassID = $maxNestValue["SubClassID"];
         $subclass =& $this->bookingClassArray[$bookingClassDateID]->subclassArray[$subclassID];
         // Note: we may have to add the $subclass->UPR here,
         // in case there are both MIN and MAX nests.
         $subclass->availability =
           min($subclass->maxNestValue-$subclass->maxCumulativeBookingsCounter,
               $avPool, $lastMaxValue);
         $lastMaxValue = $subclass->availability;
       }
     }
     // Calculate the availability on non-nested subclasses
     foreach ($this->bookingClassArray as $bookingClassIndex) {
       foreach ($bookingClassIndex->subclassArray as $subclassIndex) {
         if (!isset($subclassIndex->minNestValue) && 
             !isset($subclassIndex->maxNestValue)) {
           $subclass =& $this->bookingClassArray[$bookingClassIndex->bookingClassDateID]->subclassArray[$subclassIndex->subClassID];
           $subclass->availability = $avPool;
         }
       }
     }

   } // End of setAvailabilities() function

 } // End of SegmentCabin class

 class BookingClassDate {
   var $bookingClassDateID;
   var $bookingClassID;
   var $bookingClass;
   var $bookingClassOrder;
   var $description;
   var $effectiveDate;
   var $discontinueDate;
   var $availabilityControlType;
   var $published;
   var $receivedAvailabilityStatus;
   var $useManualStatus;
   var $firstPosting;
   var $segmentCabin;
   var $subclassArray;

   var $bookingsCounter;

   function BookingClassDate (&$db, $bookingClassDateID) {
     $this->bookingClassDateID = $bookingClassDateID;
     $this->bookingsCounter = 0.0;

     $bookingClassDateRequest = "SELECT * FROM bookingclassdate, bookingclass "
       . "WHERE bookingclassdate.bookingclassdateid='".$bookingClassDateID."' "
       . "AND bookingclass.bookingclassid=bookingclassdate.bookingclassid ";
     $bookingClassDateResult = mysql_query ($bookingClassDateRequest, $db);

     while ($bookingClassDateRow = mysql_fetch_array($bookingClassDateResult)){
       $this->bookingClassID = $bookingClassDateRow["BookingClassID"];
       $this->bookingClass = $bookingClassDateRow["BookingClass"];
       $this->bookingClassOrder = $bookingClassDateRow["BookingClassOrder"];
       $this->description = $bookingClassDateRow["Description"];
       $this->effectiveDate = $bookingClassDateRow["EffectiveDate"];
       $this->discontinueDate = $bookingClassDateRow["DiscontinueDate"];
       $this->availabilityControlType = $bookingClassDateRow["AvailabilityControlType"];
       $this->published = $bookingClassDateRow["Published"];
       $this->receivedAvailabilityStatus = $bookingClassDateRow["ReceivedAvailabilityStatus"];
       $this->useManualStatus = $bookingClassDateRow["UseManualStatus"];
       $this->firstPosting = $bookingClassDateRow["FirstPosting"];
     }

     // Get the associated subclasses
     $subclassListRequest = "SELECT SubClassID FROM subclass "
              . "WHERE subclass.bookingclassdateid='".$bookingClassDateID."' "
              . "ORDER BY subclass.subclassorder ";
     $subclassListResult = mysql_query ($subclassListRequest, $db);

     $this->subclassArray = array ();
     while ($subclassRow = mysql_fetch_array ($subclassListResult)) {
       // Add the subclasses to the array
       $this->subclassArray[$subclassRow["SubClassID"]] =& 
                                new SubClass ($db, $subclassRow["SubClassID"]);
       $this->bookingsCounter += $this->subclassArray[$subclassRow["SubClassID"]]->bookingsCounter;
     }
   }

 }

 class SubClass {
   var $subClassID;
   var $subClassOrder;
   var $bookingClassDateID;
   var $bookingClassDate;
   var $POSKeyword;
   var $bookingsCounter;
   var $waitlistCounter;
   var $notificationLevel1;
   var $notificationLevel2;
   var $roundedRealAvailability;
   var $availabilityStatus;
   var $cancellationRate;
   var $noShowPercentage;
   var $subclassNestArray;

   //var $displayMIN;
   //var $displayMAX;
   var $minNestValue;
   var $maxNestValue;
   var $minNestID;
   var $maxNestID;
   var $minCumulativeBookingsCounter;
   var $maxCumulativeBookingsCounter;
   var $UPR;
   var $committedSpace;

   function SubClass (&$db, $subClassID) {
     $this->subClassID = $subClassID;

     $subClassRequest = "SELECT * FROM subclass "
                              . "WHERE subclass.subclassid='".$subClassID."' ";
     $subClassResult = mysql_query ($subClassRequest, $db);

     while ($subClassRow = mysql_fetch_array($subClassResult)){
       $this->subClassOrder = $subClassRow["SubClassOrder"];
       $this->POSKeyword = $subClassRow["POSKeyword"];
       $this->bookingsCounter = $subClassRow["BookingsCounter"];
       $this->waitlistCounter = $subClassRow["WaitlistCounter"];
       $this->notificationLevel1 = $subClassRow["NotificationLevel1"];
       $this->notificationLevel2 = $subClassRow["NotificationLevel2"];
       $this->roundedRealAvailability = $subClassRow["RoundedRealAvailability"];
       $this->availabilityStatus = $subClassRow["AvailabilityStatus"];
       $this->cancellationRate = $subClassRow["CancellationRate"];
       $this->noShowPercentage = $subClassRow["NoShowPercentage"];
     }
   }

 }

 class SubClassNest {
   var $subClassNestID;
   var $segmentCabinID;
   var $controlType;

   function SubClassNest (&$db, $subClassNestID, $segmentCabinID) {
     $this->subClassNestID = $subClassNestID;
     $this->segmentCabinID = $segmentCabinID;
   }
 }

 class SegmentClassHolder {
   var $subClassID;
   var $bookingClassDateID;
   var $cabin;
   var $bookingClass;
   var $subClassOrder;
   var $totalSubClassOrder;
   var $bookingsCounter;
   var $availabilityControlType;
 }

?>
