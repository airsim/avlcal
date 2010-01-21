// STL
#include <cassert>
#include <iostream>
#include <sstream>
#include <fstream>
#include <string>
// AVLCAL
#include <avlcal/AVLCAL_Service.hpp>
#include <avlcal/config/avlcal-paths.hpp>

// ///////// M A I N ////////////
int main (int argc, char* argv[]) {

  try {
    
    // Airline code
    std::string lAirlineCode ("LH");
    
    // Number of passengers in the travelling group
    AVLCAL::PartySize_T lPartySize = 5;
    
    // Output log File
    std::string lLogFilename ("avlCalc.log");

    // Set the log parameters
    std::ofstream logOutputFile;
    // Open and clean the log outputfile
    logOutputFile.open (lLogFilename.c_str());
    logOutputFile.clear();
    
    // Initialise the list of classes/buckets
    const stdair::BasLogParams lLogParams (stdair::LOG::DEBUG, logOutputFile);
    AVLCAL::AVLCAL_Service avlcalService (lLogParams, lAirlineCode);

    // Perform an availability calculation
    avlcalService.avlCalculate (lPartySize);
    
  } catch (const std::exception& stde) {
    std::cerr << "Standard exception: " << stde.what() << std::endl;
    return -1;
    
  } catch (...) {
    return -1;
  }
  
  return 0;	
}
