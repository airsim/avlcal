C++ Seat Availability Calculator Library
========================================

# Summary
AvlCal aims at providing a clean API and a simple implementation
(C++ library) of an Airline-related Seat Inventory Availability
Calculation system. That library uses the Standard Airline IT
C++ object model (https://github.com/airsim/stdair).

AvlCal makes an extensive use of existing open-source libraries for
increased functionality, speed and accuracy. In particular the
Boost (C++ Standard Extensions: https://www.boost.org) library is used.

AvlCal is the one of the components of the Travel Market Simulator
(https://travel-sim.org). However, it may be used in a
stand-alone mode.

# Installation

## On Fedora/CentOS/RedHat distribution
Just use DNF (or Yum on older distributions):
```bash
$ dnf -y install avlcal-devel avlcal-doc
```

You can also get the RPM packages (which may work on Linux
distributions like Novel Suse and Mandriva) from the Fedora repository
(_e.g._, for Fedora Rawhide, 
https://fr2.rpmfind.net/linux/RPM/fedora/devel/rawhide/x86_64/)


## Building the library and test binary from Git repository
The Sourceforge Git repository may be cloned as following:
```bash
$ git clone git@github.com:airsim/avlcal.git avlcalgit # through SSH
$ git clone https://github.com/airsim/avlcal.git # if the firewall filters SSH
$ cd avlcalgit
```

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

## Building the library and test binary from the tarball
The latest stable source tarball (`avlcal*.tar.gz` or `.bz2`) can be found
on GitHub: https://github.com/airsim/avlcal/releases

To customise the following to your environment, you can alter the path
to the installation directory:
```bash
export INSTALL_BASEDIR="${HOME}/dev/deliveries"
export AVLCAL_VER="1.00.7"
export LIBSUFFIX_4_CMAKE="-DLIB_SUFFIX=64"
```

Then, as usual:
* To configure the project, type something like:
```bash
  mkdir build && cd build
  cmake -DCMAKE_INSTALL_PREFIX=${INSTALL_BASEDIR}/avlcal-${AVLCAL_VER} \
   -DWITH_STDAIR_PREFIX=${INSTALL_BASEDIR}/stdair-stable \
   -DWITH_AIRRAC_PREFIX=${INSTALL_BASEDIR}/airrac-stable \
   -DWITH_RMOL_PREFIX=${INSTALL_BASEDIR}/rmol-stable \
   -DWITH_AIRINV_PREFIX=${INSTALL_BASEDIR}/airinv-stable \
   -DCMAKE_BUILD_TYPE:STRING=Debug -DENABLE_TEST:BOOL=ON -DINSTALL_DOC:BOOL=ON \
   -DRUN_GCOV:BOOL=OFF ${LIBSUFFIX_4_CMAKE} ..
```
* To build the project, type:
```bash
  make
```
* To test the project, type:
```bash
  make check
```
* To install the library (`libavlcal*.so*`) and the binary (`avlcal`),
  just type:
``` bash
  make install
```
* To browse the (just installed, if enabled) HTML documentation:
```bash
  open file://${INSTALL_BASEDIR}/avlcal-stable/share/doc/avlcal-${AVLCAL_VER}/html/index.html
```
* To browse the (just installed, if enabled) PDF documentation:
```bash
  evince ${INSTALL_BASEDIR}/avlcal-stable/share/doc/avlcal-${AVLCAL_VER}/html/refman.pdf
```
* To package the source files, type:
```bash
  make dist
```
* To package the binary and the (HTML and PDF) documentation:
```bash
  make package
```

Denis Arnaud

