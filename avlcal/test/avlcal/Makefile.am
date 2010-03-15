## test/avlcal sub-directory
include $(top_srcdir)/Makefile.common

MAINTAINERCLEANFILES = Makefile.in

##
SUBDIRS =

EXTRA_DIST =

##
check_PROGRAMS = avlCalc
TESTS = $(check_PROGRAMS)
XFAIL_TESTS = #

#InventoryTestSuite_SOURCES = InventoryTestSuite.hpp \
				InventoryTestSuite.cpp
#InventoryTestSuite_CXXFLAGS= $(CPPUNIT_CFLAGS) $(BOOST_CFLAGS)
#InventoryTestSuite_LDADD = $(top_builddir)/test/com/libcppunitcore.la
#InventoryTestSuite_LDFLAGS = \
#		$(BOOST_LIBS) $(CPPUNIT_LIBS) \
#		$(top_builddir)/stdair/core/libstdair.la \
#		$(top_builddir)/avlcal/libavlcal.la

avlCalc_SOURCES = avlCalc.cpp
avlCalc_CXXFLAGS= $(CPPUNIT_CFLAGS) $(BOOST_CFLAGS)
avlCalc_LDADD =
avlCalc_LDFLAGS = $(BOOST_LIBS) \
		$(top_builddir)/stdair/core/libstdair.la \
		$(top_builddir)/avlcal/core/libavlcal.la
