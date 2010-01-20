// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <cassert>
// Boost
#include <boost/date_time/gregorian/gregorian.hpp>
#include <boost/date_time/posix_time/ptime.hpp>
// StdAir
#include <stdair/basic/BasChronometer.hpp>
// Avlcal
#include <avlcal/basic/BasConst_AVLCAL_Service.hpp>
#include <avlcal/command/AvailabilityCalculator.hpp>
#include <avlcal/factory/FacAvlcalServiceContext.hpp>
#include <avlcal/service/AVLCAL_ServiceContext.hpp>
#include <avlcal/service/Logger.hpp>
#include <avlcal/AVLCAL_Service.hpp>

namespace AVLCAL {

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::
  AVLCAL_Service (std::ostream& ioLogStream, const AirlineCode_T& iAirlineCode)
    : _avlcalServiceContext (NULL) {
    init (ioLogStream, iAirlineCode);
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::AVLCAL_Service ()
    : _avlcalServiceContext (NULL) {
    assert (false);
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::AVLCAL_Service (const AVLCAL_Service& iService) {
    assert (false);
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::~AVLCAL_Service () {
    // Delete/Clean all the objects from memory
    finalise();
  }

  // //////////////////////////////////////////////////////////////////////
  void logInit (const LOG::EN_LogLevel iLogLevel,
                std::ostream& ioLogOutputFile) {
    Logger::instance().setLogParameters (iLogLevel, ioLogOutputFile);
  }

  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::init (std::ostream& ioLogStream,
                             const AirlineCode_T& iAirlineCode) {
    // Set the log file
    logInit (LOG::DEBUG, ioLogStream);

    // Initialise the context
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext = 
      FacAvlcalServiceContext::instance().create (iAirlineCode);
    _avlcalServiceContext = &lAVLCAL_ServiceContext;
  }
  
  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::finalise () {
    assert (_avlcalServiceContext != NULL);
  }

  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::avlCalculate (const PartySize_T& iPartySize) {
    
    if (_avlcalServiceContext == NULL) {
      throw NonInitialisedServiceException();
    }
    assert (_avlcalServiceContext != NULL);
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext= *_avlcalServiceContext;

    try {
      
      // Retrieve the airline code
      const AirlineCode_T& lAirlineCode =
        lAVLCAL_ServiceContext.getAirlineCode();
      
      // Delegate the booking to the dedicated command
      stdair::BasChronometer lAvlCalcChronometer;
      lAvlCalcChronometer.start();
      AvailabilityCalculator::avlCalculate (lAirlineCode, iPartySize);
      const double lAvlCalcMeasure = lAvlCalcChronometer.elapsed();
      
      // DEBUG
      AVLCAL_LOG_DEBUG ("Availability Calculation: " << lAvlCalcMeasure << " - "
                        << lAVLCAL_ServiceContext.display());

    } catch (const std::exception& error) {
      AVLCAL_LOG_ERROR ("Exception: "  << error.what());
      throw AvlCalcultationException();
    }
  }
  
}
