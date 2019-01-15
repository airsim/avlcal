
Summary:
--------
AvlCal aims at providing a clean API and a simple implementation (C++
library) of an Airline-related Seat Inventory Availability
Calculation system. That library uses the Standard Airline IT C++
object model (http://sf.net/projects/stdair).

AvlCal makes an extensive use of existing open-source libraries for
increased functionality, speed and accuracy. In particular the
Boost (C++ Standard Extensions: http://www.boost.org) library is used.

AvlCal is the one of the components of the Travel Market Simulator
(http://www.travel-market-simulator). However, it may be used in a
stand-alone mode.


Getting and installing from the Fedora/CentOS/RedHat distribution:
------------------------------------------------------------------
Just use Yum:
yum -y install avlcal-devel avlcal-doc

You can also get the RPM packages (which may work on Linux
distributions like Novel Suse and Mandriva) from the Fedora repository
(e.g., for Fedora 18, 
http://fr2.rpmfind.net/linux/fedora/releases/18/Everything/)


Building the library and test binary from Git repository:
----------------------------------------------------------------
The Sourceforge Git repository may be cloned as following:
git clone ssh://git.code.sf.net/p/avlcal/code avlcalgit
cd avlcalgit
git checkout trunk

Then, you need the following packages (Fedora/RedHat/CentOS names here, 
but names may vary according to distributions):
  * cmake
  * gcc-c++
  * stdair-devel, airrac-devel, rmol-devel, airinv-devel
  * boost-devel
  * zeromq-devel
  * readline-devel, ncurses-devel
  * soci-mysql-devel
  * python-devel
  * gettext-devel (optional)
  * doxygen, ghostscript, graphviz and tetex-latex (optional)
  * rpm-build (optional)

Building the library and test binary from the tarball:
------------------------------------------------------
The latest stable source tarball (avlcal*.tar.gz or .bz2) can be found here:
http://sourceforge.net/project/showfiles.php?group_id=295245

To customise the following to your environment, you can alter the path
to the installation directory:
export INSTALL_BASEDIR=/home/user/dev/deliveries
export AVLCAL_VER=1.00.1
export LIBSUFFIX_4_CMAKE="-DLIB_SUFFIX=64"

Then, as usual:
* To configure the project, type something like:
  mkdir build && cd build
  cmake -DCMAKE_INSTALL_PREFIX=/home/user/dev/deliveries/avlcal-${AVLCAL_VER} \
   -DWITH_STDAIR_PREFIX=/home/user/dev/deliveries/stdair-stable \
   -DWITH_AIRRAC_PREFIX=/home/user/dev/deliveries/airrac-stable \
   -DWITH_RMOL_PREFIX=/home/user/dev/deliveries/rmol-stable \
   -DWITH_AIRINV_PREFIX=/home/user/dev/deliveries/airinv-stable \
   -DCMAKE_BUILD_TYPE:STRING=Debug -DENABLE_TEST:BOOL=ON -DINSTALL_DOC:BOOL=ON \
   -DRUN_GCOV:BOOL=OFF ${LIBSUFFIX_4_CMAKE} ..
* To build the project, type:
  make
* To test the project, type:
  make check
* To install the library (libavlcal*.so*) and the binary (avlcal),
  just type:
  make install
* To browse the (just installed, if enabled) HTML documentation:
  midori file://${INSTALL_BASEDIR}/avlcal-stable/share/doc/avlcal-${AVLCAL_VER}/html/index.html
* To browse the (just installed, if enabled) PDF documentation:
  evince ${INSTALL_BASEDIR}/avlcal-stable/share/doc/avlcal-${AVLCAL_VER}/html/refman.pdf
* To package the source files, type:
  make dist
* To package the binary and the (HTML and PDF) documentation:
  make package

Denis Arnaud (October 2012)

