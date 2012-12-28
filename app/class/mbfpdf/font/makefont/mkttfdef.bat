@ECHO OFF
rem -----------------------------------------------------------------------
rem Make mbttfdef.php
rem -----------------------------------------------------------------------
set PHPEXE=c:\php\cli\php.exe
IF "%1"=="" GOTO HELP
IF NOT EXIST %PHPEXE%    GOTO ERR1
IF NOT EXIST ttf2pt1.exe GOTO ERR2
IF "%1"=="remake"        GOTO PHPONLY
IF "%1"=="backup"        GOTO BACKUP
IF "%1"=="restore"       GOTO RESTORE
IF NOT EXIST %1          GOTO ERR3
ttf2pt1 %1 work
:PHPONLY
%PHPEXE% -r require('mkttfdef.php');
IF "%1"=="remake"        GOTO EXIT
del work.t1a
del work.afm
GOTO EXIT
:BACKUP
COPY /Y ..\mbttfdef.php ..\mbttfdef.bak
COPY /Y mkttfdef.dat mkttfdef.bak
GOTO EXIT
:RESTORE
COPY /Y ..\mbttfdef.bak ..\mbttfdef.php
COPY /Y mkttfdef.bak mkttfdef.dat
GOTO EXIT
:HELP
ECHO Usage: mkttfdef YourFont.TTF (Add TrueType Information)
ECHO        mkttfdef remake       (Remake mbttfdef.php file)
ECHO        mkttfdef backup       (Backup enviroment files)
ECHO        mkttfdef restore      (Restore enviroment files)
GOTO EXIT
:ERR1
ECHO Error: Not Found PHP.EXE Cli Version.
ECHO Please EDIT PHPEXE Variable for PHP(CLI PHP.EXE File Path)
ECHO Now Setting PHPEXE=%PHPEXE% ok?
GOTO EXIT
:ERR2
ECHO Error: Not Found ttf2pt1.exe
GOTO EXIT
:ERR3
ECHO Error: Not Found %1
GOTO EXIT
:EXIT