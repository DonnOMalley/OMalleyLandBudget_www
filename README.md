OMalleyLand Website
=====================
###### Latest Revision: **2014-01-19 - DØ** : *v0.9* ######
This is the OMalleyLand Home Page.<br />
We are OMalleyLand LLC as well as the O'Malley Family.<br />
This site is currently limited to only our home budget. 

***
Requires 
========
* MySQL Database (OMalleyLandBudget in code)
* Web Server
* php Server

***
Changelog
=========
**2014-01-19 - DØ** : _v1.0.0_ <br />
 * Updated all php mysql calls for mysqli
 * Modified graphs for including or omitting bills for better human analysis on the fly
 * Improved and cleaned up includes for use across all files
 * Created graphs pages for linking from other pages - Same pages used for mobile or desktop links
 * Cleaned up all queries to use constants from database include file
 * Added logging capabilities and implemented some logging
 * Resolved various date issues
<br />
	Logging implementation will grow as files are updated and troubleshooting occurs.
	Database References are sterilized to eliminate the specifics to my MySQL DB
<br />
<br />
**2013-01-27 - DØ** : _v0.9.1_ <br />
 * Added payment budget calculator based on payment/savings amounts - Same for desktop & mobile
 * ^^Still Need to fix the Catgory and Pay Period Budget Column Sorting (or Remove)
<br />
<br />
**2013-01-24 - DØ** : _v0.9_ <br />
 * Improved static/dynamic sorting for budget, graph and info pages (mobile & desktop versions)
 * Updated date sorting for graphs.
 * UI Additions for clarification
 * Updated logic for date drop downs and year roll overs(December => January)
<br />
<br />
**2012-09-05 - DØ** : _v0.8.5_ <br />
 * Added Desktop/mobile page for creating new payees
 * Added support for special characters on inserting payees/categories/debits
 * Updated more Addresses/un/pw references to be conisistent
<br />
<br />
**2012-09-03 - DØ** : _v0.8.1_ <br />
 * Reduced size of graphs on mobile graph.php page
 * Updated README to reflect changes.
<br />
<br />
**2012-09-03 - DØ** : _v0.8_ <br />
 * Added Additional validation(validate_form) to the createDebit.php page (desktop/mobile) to ensure all values are filled in
 * Created Mobile version of budget.php page (info only)
 * Added Budget graph to mobile graph.php page.
 * Updated README to reflect changes.
<br />
<br />
**2012-08-30 - DØ** : _v0.7.5_ <br />
 * Added budget.php(desktop) for displaying budget totals by category for comparison with budget/spent/remaining values
 * Cleaned up Desktop version of info.php replacing OMalleyLandBudget hard coded references with $db_name variable references
 * Updated README to reflect changes.
<br />
<br />

**2012-08-29 - DØ** : _v0.7_ <br />
 * Updated createDebit pages to sort Categories by Name
 * Updated README to reflect changes.
<br />
<br />

**2012-08-26 - DØ** : _v0.6_ <br />
 * Additional directory cleanup(removed duplicate css/png directories)
 * Updated references for directory changes
 * Converted MySQL DB References to Global Variables
 * Cleaned up MySQL Statements removing hard coded names/values where possible.
 * Updated all link references to smallest relative paths
 * Removed Checkbox Definition from CSS - No longer used.
 * Updated README to reflect changes.
<br />
<br />

**2012-08-13 - DØ** : _v0.5_ <br />
 * Added youtube link for Donn
 * Added Front Porch Cameras and Alley camera (live feeds) to home page.
<br />
<br />

**2012-08-08 - DØ** : _v0.4_ <br />
 * Added new directories for Jeni and Chris
 * Created php scripts to automatically scan art directory and build HTML Pages using support graphic formats and a .txt(html) file
<br />
<br />

**2012-07-29 - DØ** : _v0.3_ <br />
 * Updated directory structure - simplified by removing sub directories
 * Added Mobile directory and updated pages to be better formatted for mobile
 * Added directory for Chris that we will expand to use for him to have an art showcase.
 * Added sorting to createDebit combo boxes.
<br />
<br />

**2012-07-22 - DØ** : _v0.2_ <br />
 * Updated for migration to New Server 
 * Added new Home Page
 * Cleaned up README.md and added more information.
<br />
<br />

**2012 - DØ** : _Initial Release : v0.1_
