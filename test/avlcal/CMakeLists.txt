#
# AvlCal Tests
#

##
# Specify each test suite configuration

# * AvlCalTestSuite Test Suite
module_test_add_suite (avlcal AvlCalTestSuite AvlCalTestSuite.cpp)


##
# Register all the test suites to be built and performed
module_test_build_all ()

