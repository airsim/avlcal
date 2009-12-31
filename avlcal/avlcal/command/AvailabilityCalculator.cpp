// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <exception>
// Avlcal
#include <avlcal/command/AvailabilityCalculator.hpp>
#include <avlcal/service/Logger.hpp>

namespace AVLCAL {

  // //////////////////////////////////////////////////////////////////////
  void AvailabilityCalculator::avlCalculate (const AirlineCode_T& iAirlineCode,
                                             const PartySize_T& iPartySize) {

    try {

      // DEBUG
      AVLCAL_LOG_DEBUG ("An availability calculation has been performed "
                        << "for the airline " << iAirlineCode
                        << " for " << iPartySize << " passengers.");
    
    } catch (const std::exception& lStdError) {
      AVLCAL_LOG_ERROR ("Error: " << lStdError.what());
      throw AvlCalcultationException();
    }
  }

}
