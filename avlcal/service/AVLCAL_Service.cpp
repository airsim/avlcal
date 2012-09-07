// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <cassert>
// Boost
#include <boost/make_shared.hpp>
// StdAir
#include <stdair/basic/BasChronometer.hpp>
#include <stdair/basic/BasFileMgr.hpp>
#include <stdair/bom/BomManager.hpp>
#include <stdair/service/Logger.hpp>
#include <stdair/STDAIR_Service.hpp>
// Avlcal
#include <avlcal/basic/BasConst_AVLCAL_Service.hpp>
#include <avlcal/command/AvailabilityCalculator.hpp>
#include <avlcal/factory/FacAvlcalServiceContext.hpp>
#include <avlcal/service/AVLCAL_ServiceContext.hpp>
#include <avlcal/AVLCAL_Service.hpp>

namespace AVLCAL {

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::AVLCAL_Service() : _avlcalServiceContext (NULL) {
    assert (false);
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::AVLCAL_Service (const AVLCAL_Service& iService) {
    assert (false);
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::AVLCAL_Service (const stdair::BasLogParams& iLogParams,
                                  const stdair::AirlineCode_T& iAirlineCode)
    : _avlcalServiceContext (NULL) {

    // Initialise the STDAIR service handler
    stdair::STDAIR_ServicePtr_T lSTDAIR_Service_ptr =
      initStdAirService (iLogParams);
    
    // Initialise the service context
    initServiceContext (iAirlineCode);

    // Add the StdAir service context to the AVLCAL service context
    // \note AVLCAL owns the STDAIR service resources here.
    const bool ownStdairService = true;
    addStdAirService (lSTDAIR_Service_ptr, ownStdairService);
    
    // Initialise the (remaining of the) context
    initAVLCALService();
  }

  // ////////////////////////////////////////////////////////////////////
  AVLCAL_Service::
  AVLCAL_Service (stdair::STDAIR_ServicePtr_T ioSTDAIRServicePtr,
                  const stdair::AirlineCode_T& iAirlineCode)
    : _avlcalServiceContext (NULL) {
    
    // Initialise the context
    initServiceContext (iAirlineCode);
    
    // Store the STDAIR service object within the AVLCAL service context
    // \note AVLCAL does not own the STDAIR service resources here.
    const bool doesNotOwnStdairService = false;
    addStdAirService (ioSTDAIRServicePtr, doesNotOwnStdairService);
    
    // Initialise the (remaining of the) context
    initAVLCALService();
  }

  // //////////////////////////////////////////////////////////////////////
  AVLCAL_Service::~AVLCAL_Service() {
    // Delete/Clean all the objects from memory
    finalise();
  }

  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::finalise() {
    assert (_avlcalServiceContext != NULL);
    // Reset the (Boost.)Smart pointer pointing on the STDAIR_Service object.
    _avlcalServiceContext->reset();
  }

  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::
  initServiceContext (const stdair::AirlineCode_T& iAirlineCode) {
    // Initialise the context
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext = 
      FacAvlcalServiceContext::instance().create (iAirlineCode);
    _avlcalServiceContext = &lAVLCAL_ServiceContext;
  }
  
  // ////////////////////////////////////////////////////////////////////
  stdair::STDAIR_ServicePtr_T AVLCAL_Service::
  initStdAirService (const stdair::BasLogParams& iLogParams) {
    
    // Initialise the STDAIR service handler
    // Note that the track on the object memory is kept thanks to the Boost
    // Smart Pointers component.
    stdair::STDAIR_ServicePtr_T lSTDAIR_Service_ptr = 
      boost::make_shared<stdair::STDAIR_Service> (iLogParams);
    assert (lSTDAIR_Service_ptr != NULL);
    
    return lSTDAIR_Service_ptr;
  }

  // ////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::
  addStdAirService (stdair::STDAIR_ServicePtr_T ioSTDAIR_Service_ptr,
                    const bool iOwnStdairService) {

    // Retrieve the AVLCAL service context
    assert (_avlcalServiceContext != NULL);
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext = *_avlcalServiceContext;

    // Store the STDAIR service object within the (AIRINV) service context
    lAVLCAL_ServiceContext.setSTDAIR_Service (ioSTDAIR_Service_ptr,
                                              iOwnStdairService);
  }

  // ////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::initAVLCALService() {
    // Do nothing at this stage. A sample BOM tree may be built by
    // calling the buildSampleBom() method
  }

  // ////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::reset() {
    // Retrieve the AVLCAL service context
    assert (_avlcalServiceContext != NULL);
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext = *_avlcalServiceContext;

    lAVLCAL_ServiceContext.reset();
  }
  
  // //////////////////////////////////////////////////////////////////////
  void AVLCAL_Service::avlCalculate (const stdair::PartySize_T& iPartySize) {
    
    if (_avlcalServiceContext == NULL) {
      throw stdair::NonInitialisedServiceException ("The AvlCal service has not been initialised");
    }
    assert (_avlcalServiceContext != NULL);
    AVLCAL_ServiceContext& lAVLCAL_ServiceContext= *_avlcalServiceContext;

    try {
      
      // Retrieve the airline code
      const stdair::AirlineCode_T& lAirlineCode =
        lAVLCAL_ServiceContext.getAirlineCode();
      
      // Delegate the booking to the dedicated command
      stdair::BasChronometer lAvlCalcChronometer;
      lAvlCalcChronometer.start();
      AvailabilityCalculator::avlCalculate (lAirlineCode, iPartySize);
      const double lAvlCalcMeasure = lAvlCalcChronometer.elapsed();
      
      // DEBUG
      STDAIR_LOG_DEBUG ("Availability Calculation: " << lAvlCalcMeasure << " - "
                        << lAVLCAL_ServiceContext.display());

    } catch (const std::exception& error) {
      STDAIR_LOG_ERROR ("Exception: "  << error.what());
      throw AvlCalcultationException();
    }
  }
  
}
