<?php
 require ("connection.inc.php");
 require ("class.inc.php");
 session_start ();
 require ("flight_functions.php");
 require ("leg_functions.php");
 require ("segment_functions.php");

 $currentFlightDate =& $flightDateArray[$FlightDateID];
 $currentSegmentDate =& $currentFlightDate->segmentDateArray[$SegmentDateID];
 $segmentDetails = $currentSegmentDate->departureStation.$currentSegmentDate->arrivalStation;

 //****
 //* A control has been changed
 //*
 if (isset ($changedValue)) {
  $changedSegmentCabin =& $currentSegmentDate->segmentCabinArray[$changedCabinID];
  $changedSubclass =& $changedSegmentCabin->bookingClassArray[$changedBookingClassDateID]->subclassArray[$changedSubclassID];

  if ($changedNestType == "MIN") {
    $changedSubclass->minNestValue = $changedValue;
  } else if ($changedNestType == "MAX") {
    $changedSubclass->maxNestValue = $changedValue;
  }


  //**** Re-Calculate the availabilities ****
  $changedSegmentCabin->setUPRs ();
  $changedSegmentCabin->setCommittedSpaces ();

  // Set the bookings counters within the leg-dates/cabins
  setLegBookingsCounters ($flightDateArray, $FlightDateID);

  // Set the AvPools within the leg-dates/cabins
  setLegAvPools ($flightDateArray, $FlightDateID);

  // Set the availabilities within the segment-dates/cabins
  setSegmentAvailabilities ($currentFlightDate);
 }
?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Segments Details for Segment <?= $segmentDetails ?></title>
  <script language="JavaScript">
    <!--
    function changeValue (oldValue, cabinID, bookingClassDateID, subclassID, 
                          nestType) {
      newValue = window.prompt ("Enter the new value", oldValue);
      if (newValue==null || newValue=="null" || newValue=="") {
        return;
      }
      document.valueForm.changedValue.value = newValue;
      document.valueForm.changedCabinID.value = cabinID;
      document.valueForm.changedBookingClassDateID.value = bookingClassDateID;
      document.valueForm.changedSubclassID.value = subclassID;
      document.valueForm.changedNestType.value = nestType;
      document.valueForm.submit ();
    }
    // -->
  </script>
</head>

<body bgcolor="#FFFFFF" vlink="#0000FF" alink="#0000FF">

<center><h3>Segments Details for Segment <?= $segmentDetails ?></h3></center>

<form method="post" action="<?= basename ($PHP_SELF) ?>" name="valueForm">
 <input type="hidden" name="PHPSESSID" value="<?= session_id () ?>">
 <input type="hidden" name="FlightDateID" value="<?= $FlightDateID ?>">
 <input type="hidden" name="SegmentDateID" value="<?= $SegmentDateID ?>">
 <input type="hidden" name="changedValue" value="">
 <input type="hidden" name="changedCabinID" value="">
 <input type="hidden" name="changedBookingClassDateID" value="">
 <input type="hidden" name="changedSubclassID" value="">
 <input type="hidden" name="changedNestType" value="">


<table border="0" cellspacing="0" cellpadding="0" width="100%">
 <tr>
  <td bgcolor="#DCDCDC">
   <table border="0" cellpadding="2" width="100%">
    <tr bgcolor="#EEEEEE">
     <!-- td>&nbsp;</td -->
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>CPT</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>SubClass</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>POS</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>MIN</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>MAX</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>Bkgs</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>Avl Ctl Type</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>UPR</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>CommSpace</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>AvPool</b>
      </font></th>
      <th align="center"><font face="Arial,Helvetica" size="-1">
       <b>Avl</b>
      </font></th>
    </tr>

<?php
/*
 function aHrefBuild ($oldValue, $changedCabinID, $changedBookingClassDateID,
                      $changedSubclassID, $displayString, $changedNestType) {
   $aHrefBegin = "<a href=\"#\" onClick=\"javascript:changeValue('";
   $aHrefEnd = "</a>";
   $aHrefString = $aHrefBegin.$oldValue."', ".$changedCabinID
                 .", ".$changedBookingClassDateID.", ".$changedSubclassID
                 .", '".$changedNestType."')\">"
                 .$displayString.$aHrefEnd;
   return $aHrefString;
 }
*/
 function aHrefBuild ($oldValue, $changedCabinID, $changedBookingClassDateID,
                      $changedSubclassID, $displayString, $changedNestType) {
   $aHrefBegin = "<input type=\"button\" onClick=\"javascript:changeValue('";
   $aHrefEnd = ">";
   $aHrefString = $aHrefBegin.$oldValue."', ".$changedCabinID
                 .", ".$changedBookingClassDateID.", ".$changedSubclassID
                 .", '".$changedNestType."')\" value=\"".$displayString."\">";
   return $aHrefString;
 }


 // Display the results in a HTML table
 foreach ($currentSegmentDate->segmentCabinArray as $segmentCabin) {
   foreach ($segmentCabin->bookingClassArray as $bookingClass) {
     foreach ($bookingClass->subclassArray as $subclass) {
       $subclassOrder = 10000*$segmentCabin->cabinID
                      + 100*$bookingClass->bookingClassOrder
                      + $subclass->subClassOrder;
?>
    <tr bgcolor="#FFFFFF">
     <td align="center"><font size="-1"><?= $segmentCabin->cabin ?>
     </font></td>
     <td align="center"><font size="-1">
       <?= $bookingClass->bookingClass.$subclass->subClassOrder ?>
     </font></td>
     <td align="center"><font size="-1"><?= $subclass->POSKeyword ?>
     </font></td>
     <td align="center"><font size="-1">
      <?= (isset($subclass->minNestValue))?
        aHrefBuild ($subclass->minNestValue, $segmentCabin->cabinID,
                    $bookingClass->bookingClassDateID, $subclass->subClassID, 
                    $subclass->minNestValue." ".chr(64+$subclass->minNestID),
                    "MIN"):"&nbsp;" ?>
     </font></td>
     <td align="center"><font size="-1">
      <?= (isset($subclass->maxNestValue))?
        aHrefBuild ($subclass->maxNestValue, $segmentCabin->cabinID,
                    $bookingClass->bookingClassDateID, $subclass->subClassID, 
                    $subclass->maxNestValue." ".chr(64+$subclass->maxNestID),
                    "MAX"):"&nbsp;" ?>
     </font></td>
     <td align="center"><font size="-1"><?= $subclass->bookingsCounter ?>
     </font></td>
     <td align="center"><font size="-1">
       <?= $bookingClass->availabilityControlType ?>
     </font></td>
     <td align="center"><font size="-1">
     <?= (isset($subclass->UPR))?$subclass->UPR:"&nbsp;" ?>
     </font></td>
     <td align="center"><font size="-1"></font></td>
     <td align="center"><font size="-1"></font></td>
     <td align="center"><font size="-1">
     <?= (isset($subclass->availability))?$subclass->availability:"&nbsp;" ?>
     </font></td>
    </tr>

<?php
     }
   }
   // Display the totals for the cabin if it is not empty
   if (count ($segmentCabin->bookingClassArray) != 0) {
     // Calculate the AvPool for the leg corresponding to the segment
     // (when the segment is mono-leg)
     $segmentRouting =& 
                   $currentSegmentDate->getSegmentRouting ($currentFlightDate);
     if (count ($segmentRouting) == 1) {
       foreach ($segmentRouting as $legDate) {
         $avPool = $legDate->legCabinArray[$segmentCabin->cabinID]->avPool;
       }
     }
?>
    <tr bgcolor="#F0F0F0">
     <td align="center"><font size="-1">
       <b>Total <?= $segmentCabin->cabin ?></b>
     </font></td>
     <td align="center"><font size="-1"><?= $segmentCabin->segmentCabinID ?>
     </font></td>
     <td align="center"><font size="-1">&nbsp;</font></td>
     <td align="center"><font size="-1">&nbsp;</font></td>
     <td align="center"><font size="-1">&nbsp;</font></td>
     <td align="center"><font size="-1">
       <b><?=$segmentCabin->bookingsCounter ?></b>
     </font></td>
     <td align="center"><font size="-1">&nbsp;</font></td>
     <td align="center"><font size="-1"><b><?= $segmentCabin->UPR ?></b>
     </font></td>
     <td align="center"><font size="-1">
       <b><?= $segmentCabin->committedSpace ?></b>
     </font></td>
     <td align="center"><font size="-1"><b><?= $avPool ?></b></font></td>
     <td align="center"><font size="-1">&nbsp;</font></td>
    </tr>

<?php
   }
 }
?>

   </table>
  </td>
 </tr>
</table>

</form>

<pre>
<?php
 // print_r ($currentFlightDate);
?>
</pre>

</body>
</html>


