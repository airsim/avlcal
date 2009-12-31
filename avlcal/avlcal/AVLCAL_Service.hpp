#ifndef __AVLCAL_SVC_AVLCAL_SERVICE_HPP
#define __AVLCAL_SVC_AVLCAL_SERVICE_HPP

// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <ostream>
#include <string>
// Avlcal
#include <avlcal/AVLCAL_Types.hpp>

namespace AVLCAL {

  // Forward declaration
  class AVLCAL_ServiceContext;

  
  /** Interface for the AVLCAL Services. */
  class AVLCAL_Service {
  public:
    // /////////// Business Methods /////////////
    /** Perform a seat availability calculation. */
    void avlCalculate (const PartySize_T&);

    
    // ////////// Constructors and destructors //////////
    /** Constructor.
        @param std::ostream& Output log stream (for instance, std::cout)
        @param AirlineCode_T& Code of the owner airline. */
    AVLCAL_Service (std::ostream& ioLogStream, const AirlineCode_T&);

    /** Destructor. */
    ~AVLCAL_Service();

    
  private:
    // /////// Construction and Destruction helper methods ///////
    /** Default constructor. */
    AVLCAL_Service ();
    /** Default copy constructor. */
    AVLCAL_Service (const AVLCAL_Service&);

    /** Initialise. */
    void init (std::ostream& ioLogStream, const AirlineCode_T&);

    /** Finalise. */
    void finalise ();

    
  private:
    // ///////// Service Context /////////
    /** Avlcal context. */
    AVLCAL_ServiceContext* _avlcalServiceContext;
  };
}
#endif // __AVLCAL_SVC_AVLCAL_SERVICE_HPP
