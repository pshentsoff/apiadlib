<<<<<<< HEAD:README

API library for advertise at Yandex.Direct and  Google.AdWords


- Project's Blog (russian): http://apiadlib.pshentsoff.ru
- Contributors: pshentsoff
- Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FGRFBSFEW5V3Y
- Tags: APIAdLib, AMS, Yandex.Direct, Google.AdWords
- Author: pshentsoff
- Author's homepage: http://pshentsoff.ru
- License: Apache License, Version 2.0
- License URI: http://www.apache.org/licenses/LICENSE-2.0.html

version 0.2.1-dev from 13.05.2012
=======
## APIAdLib

API Library for common Advertisement services (Google AdWords, Yandex.Direct)

### Version history:

#### version 0.2.2-dev
-   YDirectUtils

#### version 0.2.1-dev from 13.05.2012
>>>>>>> a99759b008432d3f90f2fdbc2fefe070adf5d279:README.md
  - YDirectSoapClientFactory - factory now works with undefined method calls, but
    without arguments. Later make it more compatible with GAW library.
  - User->__call() - corrected.
  - YDirectSoapClientFactory can calls undefined functions, but without arguments
    passed (see sample /apiadlib/Samples/yd-libext-usercall-check1.php).
  BUG: 13.05.2012 14:40:23 Some result arrays needs to reset (as GetClientsUnits())  
  TODO:
  - 13.05.2012 11:23:03 Write analog of yd-common-check sample & checks ads systems
    but apiadlib based.
  - 05.04.2012 17:32:54 YD: accept only ns1:API - functions and parameters, 
    others ENV append automaticaly.
  - 06.04.2012 13:45:05 XML accepted params needs to uniqually extends GAW
  - 12.05.2012 13:39:23 correct work with logs at YDirectUserExt:
    protected 'defaultLogsDir' => 
      string 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx\apiadlib\YDirectExt\Lib' (length=52)
    protected 'logsRelativePathBase' => 
      string 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx\apiadlib\YDirectExt\Lib' (length=52)
  - 12.05.2012 15:11:39 YDirectSoapClientFactory undefined calls must process 
    with arguments too.
    
#### version 0.2.0-dev from 10.04.2012
  - <Lib>Ext library extensions added. Recommends to use more flexible and usable
    'Ext' extensions of library classes. <Lib>Ext extensions oriented to work with
    XML data exchange with ads services.
  - <Lib>UserExt extends <Lib>User classes:
    - authentication data and settings now can be passed as XML file or XML data.
    - they can be passed as parametred arrays too.
    - previous variant with INI file also acceptable.
  - <Lib>Exception extends Exception. <Lib>Ext now throwing exceptions if errors
    occurs.
  - Added common library static constants include file apiadlib.constants.inc
  - Some constant parameters moved to constants include file apiadlib.constants.inc
  - Added some usefull functions to new library file apiadlib.functions.inc
  - Changed some scopes at YDirect classes
   
#### version 0.1.2-dev from 28.03.2012
  - changed APIAdLibAutoload class - now it simply scans all 'known' for classes
    directories, not only 'Lib' paths: easy way to load any class in library.
  - added apiadlib.info file for using library in Drupal as external library (now
    not only as AMS branch, and this file only as start)
  - all AMS branches removed due to restructurization 
  
#### version 0.1.1-dev from 28.03.2012
  - added work samples:
    - yd-lib-soap-check1.php
    - yd-lib-auto-soap-check1.php
  - spl_autoload class added: class APIAdLibAutoload (apiadlib.autoload.php)

#### version 0.1-dev
  - first dev branch on github.
  - PHP #### version >= 5.2 required
  - GAW & Common Library stay unchanged, full functional. 
  - Main development is in Yandex.Direct direction. Minimum functionality:
    - Main classes released:
        - Lib/YDirectUser
        - Lib/YDirectSoapClient
        - Lib/YDirectSoapClientFactory
        - Util/YDirectReportUtils
  - path to yandex.direct auth.ini sends as param to YDirectUser class constructor
  - support of sandbox for Yandex.Direct #### version v4 (#### version live not tested)
  - support of SOAP+OAUTH Yandex.Direct #### version v4 (#### version live not tested)
  

  
 
  
