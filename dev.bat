@echo off

set cmd=%1
set allArgs=%*
for /f "tokens=1,* delims= " %%a in ("%*") do set allArgsExceptFirst=%%b
set secondArg=%2
set valid=false

:: If no command provided, list commands
if "%cmd%" == "" (
    set valid=true
    echo The following commands are available:
    echo   .\dev up
    echo   .\dev down
    echo   .\dev test
    echo   .\dev phpstan [args]
    echo   .\dev phpunit [args]
    echo   .\dev cli [args]
    echo   .\dev composer [args]
    echo   .\dev login [args]
)

:: If command is up we need to run the docker containers and install composer
if "%cmd%" == "up" (
    set valid=true
    call :up
)

:: If the command is down, then we want to stop docker
if "%cmd%" == "down" (
    set valid=true
    docker-compose -f docker-compose.yml -p twig-markdown down
)

:: Run test if requested
if "%cmd%" == "test" (
    set valid=true
    call :phpstan
    call :phpunit
)

:: Run phpstan if requested
if "%cmd%" == "phpstan" (
    set valid=true
    call :phpstan
)

:: Run phpunit if requested
if "%cmd%" == "phpunit" (
    set valid=true
    call :phpunit
)

:: Run cli if requested
if "%cmd%" == "cli" (
    set valid=true
    docker exec -it --user root --workdir /app php-twig-markdown bash -c "php %allArgs%"
)

:: Run composer if requested
if "%cmd%" == "composer" (
    set valid=true
    docker exec -it --user root --workdir /app php-twig-markdown bash -c "%allArgs%"
)

:: Login to a container if requested
if "%cmd%" == "login" (
    set valid=true
    docker exec -it --user root %secondArg%-twig-markdown bash
)

:: If there was no valid command found, warn user
if not "%valid%" == "true" (
    echo Specified command not found
    exit /b 1
)

:: Exit with no error
exit /b 0

:: Up function
:up
    docker-compose -f docker-compose.yml -p twig-markdown up -d
    docker exec -it --user root --workdir /app php-twig-markdown bash -c "cd /app && composer install"
exit /b 0

:: phpstan function
:phpstan
    docker exec -it --user root --workdir /app php-twig-markdown bash -c "chmod +x /app/vendor/bin/phpstan && /app/vendor/bin/phpstan analyse src %allArgsExceptFirst%"
exit /b 0

:: phpunit function
:phpunit
    docker exec -it --user root --workdir /app php-twig-markdown bash -c "chmod +x /app/vendor/bin/phpunit && /app/vendor/bin/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%"
exit /b 0
