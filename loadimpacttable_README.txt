Roberto 2013-11-20

Documentación sobre cómo realizar la carga de datos de Impact Factors en la base de datos de producción
científica Sciprod.



* Intro

La carga de datos la realiza el script loadimpacttable.php. El script esta dividido en varias bloques, que deben 
ejecutarse uno detras de otro. Estos bloques van procesando los datos antes de la carga final ejecutada en 
el último bloque.
La base de datos se llama 'sciprod'. Para trabajar sobre ella podemos usar phpMyAdmin desde el 
link http://irbsvr2/phpMyAdmin/ (user y password en Keepass).
El script loadimpacttable.php, el fichero de datos de conexión db_connection_parameters.txt-dist y este
fichero readme loadimpacttable_README.txt se encuentra en el repositorio de software de IRB Barcelona
en Google Code http://code.google.com, con nombre de proyecto



* Carga de los datos en la tabla de trabajo de la BD de sciprod -jos_sci_upload_impact_factor-

Limpiar (empty) los datos de la tabla jos_sci_upload_impact_factor de la base de datos 'sciprod'.
El fichero de impact factors a cargar debe salvarse en formato CSV con dos columnas, normalmente son 'Titulo abreviado' y 'factor de impacto' aunque 
esto depende de donde se haya generado el fichero. Para ello abrirlo con Excel, limpiar filas y columnas innecesarias y salvarlo en
formato CSV en \\fs-its\admin\tmp\if_2012.csv (actualizar el año de trabajo)
Este fichero se importará (import) en la tabla auxiliar jos_sci_upload_impact_factor con lo que esta tabla contendrá los datos a cargar en la BD.
El resultado de los datos cargados debe ser similar a esta exportación de datos:

INSERT INTO `jos_sci_upload_impact_factor` (`journal`, `impact_factor`) VALUES
('4OR-Q J OPER RES', '0.73'),
('AAOHN J', '0.856'),
('AAOHN J', '0.856'),
('AAPG BULL', '1.768'),
('AAPG BULL', '1.768'),
('AAPG BULL', '1.768'),
('AAPS J', '4.386'),
('AAPS PHARMSCITECH', '1.584'),
('AATCC REV', '0.354'),
...

Como se ha realizado una limpieza de filas y columnas puede haber filas repetidas. No es importante; este problema ya se tiene
en cuenta en el script loadimpacttable.php.



* Actualizar el fichero db_connection_parameters.txt

Al hacer un checkout del código de loadimpacttable.php desde htt://code.google.com se crea también un archivo
db_connection_parameters.txt-dist. Renombrar el archivo db_connection_parameters.txt-dist a db_connection_parameters.txt
Leer el fichero db_connection_parameters_README.txt
Actualizar los datos de conexión a la BD en el archivo db_connection_parameters.txt antes de ejecutar el script.
Los datos del fichero están en formato user1,pass1,user2,pass2,user3,pass3...



* Ejecutar la inclusión de los journals en la BD.

Descomentar en el script loadimpacttable.php el bloque de codigo 'Cargando journals' y ejecutar el script
desde un navegador accediendo a http://<server>/irbtools/loadimpacttable.php



* Ejecutar 'Actualizando el order'

Descomentar en el script loadimpacttable.php el bloque de codigo 'Actualizando el order' y ejecutar el script
desde un navegador accediendo a http://<server>/irbtools/loadimpacttable.php



* Ejecutar 'Cargando impact factors'

Descomentar en el script loadimpacttable.php el bloque de codigo 'Cargando impact factors' y actualizar la variable $my_year con el año de trabajo.
Ejecutar el script desde un navegador accediendo a http://<server>/irbtools/loadimpacttable.php