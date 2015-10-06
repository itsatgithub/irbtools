Fichero de modificaciones sobre los datos de IRBPeople

Instrucciones de uso:

Las modificaciones están divididas en 4 tipos de registro:

1.- registros de tipo 'add': Son registros a añadir a la información de IRBPeople. Estos registros contienen 
toda la información que debe de aparecer en la web de un usuario. También se usa para mostrar al usuario 
en un departamento añadidoal que tiene en IRBPeople.

2.- registros de tipo 'del': Son registros a borrar de IRBPeople. Se indica únicamente el identificador del
usuario que debe de eliminarse de la web.

3.- registros de tipo 'mod': Son registros que sustituyen a la información de IRBPeople. Se usan para cambiar datos
de IRBPeople por otros que serán mostrados en la web.

4.- registros de tipo 'ins': Son registros nuevos. Se incluye, como en el caso de 'add' toda la información del 
usuario. IMPORTANTE: el campo identificador de usuario debe de ser único y del tipo '999xx' 
donde 'xx' va de '01' a '99'.

Para actualizar el script:
svn export http://irbbarcelona.unfuddle.com/svn/irbbarcelona_dirtools/trunk/irbpeople2biko.php
