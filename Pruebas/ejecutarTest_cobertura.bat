:Ejecuta un test de cobertura, generando un fichero html en el que visualizar los resultados
@echo off
cd C:\wamp\www\AutoCorreccionJava_TFG
php phpunit-old.phar --coverage-html coverage tests/TestCase

echo.
echo   ------------------------------------TEST COBERTURA -----------------------------------
echo   VER RESULTADOS: Copiar esta ruta en el navegador de archivos.
echo.
echo    C:/wamp/www/AutoCorreccionJava_TFG/coverage/index.html
echo. 
 