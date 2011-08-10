#ifndef __RMOL_SVC_RMOL_SERVICE_HPP
#define __RMOL_SVC_RMOL_SERVICE_HPP

// //////////////////////////////////////////////////////////////////////
// Import section
// //////////////////////////////////////////////////////////////////////
// STL
#include <string>
// StdAir
#include <stdair/stdair_basic_types.hpp>
#include <stdair/stdair_inventory_types.hpp>
#include <stdair/stdair_service_types.hpp>
#include <stdair/basic/ForecastingMethod.hpp>
// RMOL
#include <rmol/RMOL_Types.hpp>

/// Forward declarations
namespace stdair {
  class FlightDate;
  struct BasLogParams;
  struct BasDBParams;
}

namespace RMOL {

  /// Forward declarations
  class RMOL_ServiceContext;

  /**
   * @brief Interface for the RMOL Services.
   */
  class RMOL_Service {
  public:
    // ////////// Constructors and destructors //////////
    /**
     * Constructor.
     *
     * The initRmolService() method is called; see the corresponding
     * documentation for more details.
     *
     * A reference on an output stream is given, so that log outputs
     * can be directed onto that stream.
     *
     * Moreover, database connection parameters are given, so that a
     * session can be created on the corresponding database.
     *
     * @param const stdair::BasLogParams& Parameters for the output log stream.
     * @param const stdair::BasDBParams& Parameters for the database access.
     */
    RMOL_Service (const stdair::BasLogParams&, const stdair::BasDBParams&);

    /**
     * Constructor.
     *
     * The initRmolService() method is called; see the corresponding
     * documentation for more details.
     *
     * Moreover, a reference on an output stream is given, so
     * that log outputs can be directed onto that stream.
     *
     * @param const stdair::BasLogParams& Parameters for the output log stream.
     */
    RMOL_Service (const stdair::BasLogParams&);

    /**
     * Constructor.
     *
     * The initRmolService() method is called; see the corresponding
     * documentation for more details.
     *
     * Moreover, as no reference on any output stream is given,
     * it is assumed that the StdAir log service has already been
     * initialised with the proper log output stream by some other
     * methods in the calling chain (for instance, when the RMOL_Service
     * is itself being initialised by another library service such as
     * AIRINV_Service).
     *
     * @param STDAIR_ServicePtr_T the shared pointer of stdair service.
     */
    RMOL_Service (stdair::STDAIR_ServicePtr_T);
        
    /**
     * Parse the optimisation-related data and load them into memory.
     *
     * First, the STDAIR_Service::buildDummyInventory() method is
     * called, for RMOL and with the given cabin capacity, in order to build
     * the miminum required flight-date structure in order to perform
     * an optimisation on a leg-cabin.
     *
     * The CSV input file describes the problem to be optimised, i.e.:
     * <ul>
     *   <li>the demand specifications for all the booking classes
     *       (mean and standard deviations for the demand distribution);
     *   </li>the yields corresponding to those booking classes.
     * </ul>
     *
     * That CSV file is parsed and instantiated in memory accordingly.
     * The leg-cabin capacity has been set at the initialisation of the
     * (RMOL) service.
     *
     * @param const stdair::CabinCapacity& Capacity of the leg-cabin
     *        to be optimised.
     * @param const stdair::Filename_T& (CSV) input file.
     */
    void parseAndLoad (const stdair::CabinCapacity_T& iCabinCapacity,
                       const stdair::Filename_T& iDemandAndClassDataFile);

    /**
     * Set up the StudyStatManager.
     */
    void setUpStudyStatManager();

    /**
     * Destructor.
     */
    ~RMOL_Service();


  public:
    // /////////////// Business Methods /////////////////
    /**
     * Build a sample BOM tree, and attach it to the BomRoot instance.
     *
     * \see stdair::CmdBomManager::buildSampleBom() for more details.
     *
     * Like for the parseAndLoad() method above, a dummy inventory,
     * containing a single leg-cabin, is built as well. That leg-cabin
     * has got the capacity given as parameter.
     *
     * @param const CabinCapacity_T Capacity of the single cabin.
     */
    void buildSampleBom (const stdair::CabinCapacity_T& iCabinCapacity = 0);

    /**
     * Single resource optimization using the Monte Carlo algorithm.
     */
    void optimalOptimisationByMCIntegration (const int K);

    /**
     * Single resource optimization using dynamic programming.
     */
    void optimalOptimisationByDP();

    /**
     * Single resource optimization using EMSR heuristic.
     */
    void heuristicOptimisationByEmsr();
    
    /**
     * Single resource optimization using EMSR-a heuristic.
     */
    void heuristicOptimisationByEmsrA();

    /**
     * Single resource optimization using EMSR-b heuristic.
     */
    void heuristicOptimisationByEmsrB();

    /**
     * Optimise (revenue management) an flight-date/network-date
     */
    bool optimise (stdair::FlightDate&, const stdair::DateTime_T&,
                   const stdair::ForecastingMethod&);


  public:
    // //////////////// Export support methods /////////////////
    /**
     * Recursively dump, in the returned string and in JSON format,
     * the flight-date corresponding to the parameters given as input.
     *
     * @param const stdair::AirlineCode_T& Airline code of the flight to dump.
     * @param const stdair::FlightNumber_T& Flight number of the
     *        flight to dump.
     * @param const stdair::Date_T& Departure date of a flight to dump.
     * @return std::string Output string in which the BOM tree is JSON-ified.
     */
    std::string jsonExport (const stdair::AirlineCode_T&,
                            const stdair::FlightNumber_T&,
                            const stdair::Date_T& iDepartureDate) const;


  public:
    // //////////////// Display support methods /////////////////
    /**
     * Recursively display (dump in the returned string) the objects
     * of the BOM tree.
     *
     * @return std::string Output string in which the BOM tree is
     *        logged/dumped.
     */
    std::string csvDisplay() const;


  private:
    // /////// Construction and Destruction helper methods ///////
    /**
     * Default constructor.
     */
    RMOL_Service();

    /**
     * Copy constructor.
     */
    RMOL_Service (const RMOL_Service&);

    /**
     * Initialise the STDAIR service (including the log service).
     *
     * A reference on the root of the BOM tree, namely the BomRoot object,
     * is stored within the service context for later use.
     *
     * @param const stdair::BasLogParams& Parameters for the output log stream.
     * @param const stdair::BasDBParams& Parameters for the database access.
     */
    stdair::STDAIR_ServicePtr_T initStdAirService (const stdair::BasLogParams&,
                                                   const stdair::BasDBParams&);
    
    /**
     * Initialise the STDAIR service (including the log service).
     *
     * A reference on the root of the BOM tree, namely the BomRoot object,
     * is stored within the service context for later use.
     *
     * @param const stdair::BasLogParams& Parameters for the output log stream.
     */
    stdair::STDAIR_ServicePtr_T initStdAirService (const stdair::BasLogParams&);
    
    /**
     * Attach the STDAIR service (holding the log and database services) to
     * the RMOL_Service.
     *
     * @param stdair::STDAIR_ServicePtr_T Reference on the STDAIR service.
     * @param const bool State whether or not AirInv owns the STDAIR service
     *        resources.
     */
    void addStdAirService (stdair::STDAIR_ServicePtr_T,
                           const bool iOwnStdairService);

    /**
     * Initialise the (RMOL) service context (i.e., the
     * RMOL_ServiceContext object).
     */
    void initServiceContext();
    
    /**
     * Initialise.
     *
     * Nothing is being done at that stage. The buildSampleBom() method may
     * be called later.
     */
    void initRmolService();

    /**
     * Finalise.
     */
    void finalise();


  private:
    // ////////// Service Context //////////
    /**
     * Service Context.
     */
    RMOL_ServiceContext* _rmolServiceContext;
  };
}
#endif // __RMOL_SVC_RMOL_SERVICE_HPP
