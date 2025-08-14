@echo off
REM Batch file to run Laravel Artisan commands

REM Set the path to PHP executable
SET PHP_PATH=d:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe

REM Run the artisan command with all arguments passed to this batch file
"%PHP_PATH%" artisan %*

REM Exit with the same error code as the PHP command
exit /b %errorlevel%