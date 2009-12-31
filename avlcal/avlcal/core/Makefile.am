# avlcal/core
include $(top_srcdir)/Makefile.common
include $(srcdir)/sources.mk

## Source directory

MAINTAINERCLEANFILES = Makefile.in

SUBDIRS =


# Library
lib_LTLIBRARIES = libavlcal.la

libavlcal_la_SOURCES = $(service_h_sources) $(service_cc_sources)
libavlcal_la_LIBADD = \
	$(top_builddir)/avlcal/basic/libbas.la \
	$(top_builddir)/avlcal/bom/libbom.la \
	$(top_builddir)/avlcal/factory/libfac.la \
	$(top_builddir)/avlcal/command/libcmd.la \
	$(top_builddir)/avlcal/service/libsvc.la
libavlcal_la_LDFLAGS = \
	$(BOOST_DATE_TIME_LIB) $(BOOST_PROGRAM_OPTIONS_LIB) \
	-version-info $(GENERIC_LIBRARY_VERSION)

# Header files
#pkginclude_HEADERS = $(service_h_sources)
#nobase_nodist_pkginclude_HEADERS = $(top_builddir)/avlcal/config.h

