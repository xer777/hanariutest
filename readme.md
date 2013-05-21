Hanariu - fork of Kohana Framework 3.3



Whats new:

-   Namespaces

-   Autoload from Fuel

-   Renamed System folder to Core, Application to App

-   Clean: real Core, no HTML, View, File, Feed...

-   All those missing old System classes added to module Core

-   modules/init.php changed to modules/bootstrap.php

-   added Core\\bootstrap.php

-   REST controller



Why?

To have clean Core for replacing default libraries with some others.

In example:

-   Database -\> Cabinet or Aura/Marshal or Aurora

-   Session -\> Aura/Session

-   Date -\> ExpressiveDate

-   Auth -\> HybridAuth

-   Validation -\> Respect\\Validation

...



Cleaner Core is also a better start for REST backend or any other project: just
grab Core and use any package you want.
