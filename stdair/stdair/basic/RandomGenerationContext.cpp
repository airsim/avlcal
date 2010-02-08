// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <iosfwd>
// STDAIR
#include <stdair/basic/RandomGenerationContext.hpp>

namespace stdair {

  // //////////////////////////////////////////////////////////////////////
  RandomGenerationContext::RandomGenerationContext ()
    : _cumulativeProbabilitySoFar (0.0),
      _numberOfRequestsGeneratedSoFar (0) {
  }
  
  // //////////////////////////////////////////////////////////////////////
  RandomGenerationContext::~RandomGenerationContext () {
  }
    
  // //////////////////////////////////////////////////////////////////////
  void RandomGenerationContext::incrementGeneratedRequestsCounter () {
    _numberOfRequestsGeneratedSoFar++;
  }

}
