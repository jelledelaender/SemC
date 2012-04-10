::::::::::: SemC PHP :::::::::::

:: Supported Servers

Any webserver with PHP4 or PHP5 support.

:: Possible issues

This implementation is using the `execute` method to access memory, hard-disk state etc.
It is possible this setting is disabled on your system by default.

If you are unable to enable this setting, or you don't want to enable this feature, it's possible to use a small bridge.

Proof of concept:
	- Another script (Bash, …) that is run periodically on your server, performs the required test and saves the results in a database, text-file, …
	- The SemC implementation can now simply use the results stored in this file, database, ...

:: Credits: Jelle De Laender