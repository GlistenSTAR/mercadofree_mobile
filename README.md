Plataforma de marketplace MercadoFree
========================

Bienvenidos al repositorio de código de la plataforma marketplace MercadoFree. Esta plataforma está desarrollada sobre Symfony 2.8.52, utilizando casi todos los componentes principales de Symfony en su versión estándar (Symfony Standard Edition).

Instalación en local con Windows10
--------------

Para instalar la aplicación en local para su desarrollo, con sistema operativo Windows 10, se deberán seguir los siguientes pasos:

  * Instalar XAMPP 7.2.27 (No otra, solo esta, para obtener la versión PHP 7.2)

  * Una vez instalado XAMPP y arrancado Apache y MySQL, abrir el terminal y posicionarse en el directorio C:\xampp\htdocs, y ejecutar el siguiente comando:

  <pre>
  git clone https://github.com/necobm/mercadofree.git mercadofree
  </pre>
  
  Esto nos descarga el repositorio y lo copia en el directorio C:\xampp\htdocs\mercadofree.
  
Acciones a realizar antes de instalar los vendors
--------------  

Antes de instalar los vendors con composer hay que modificar las siguientes configuraciones en el fichero "C:\xampp\php\php.ini"

1- memory_limit = -1

2- max_execution_time=120

Reiniciar el Apache mediante el Panel de Control de XAMPP

Carga de la base de datos en local
--------------  

Primeramente es necesario crear una base de datos para la aplicación. Para ello, entra a la base de datos mediante phpMyAdmin (http://localhost/phpmyadmin/) o desde tu cliente de base de datos preferido.

Una vez creada la base de datos y el usuario asociado a dicha base de datos, podrías cargar en dicha base de datos el script correspondiente a un backup reciente con todos los datos y estructura de tablas de MercadoFree, o dejarla en blanco para generar la estructura de tablas más adelante.

Instalación de vendors con Composer
--------------  
  
El próximo paso sería instalar los vendors de Symfony necesarios para el proyecto, para ello nos posicionamos en el directorio C:\xampp\htdocs\mercadofree y ejecutamos el siguiente comando:

  <pre>
  composer install
  </pre> 
  
  Esto nos descarga e instala todos los vendors, y al final, nos pedirá los siguientes parámetros de configuración, para el fichero parameters.yml:
  
  1- database_host: localhost
  
  2- database_port: (Enter, para tomar valor por defecto)
  
  3- database_name: (Nombre de la base de datos que has creado en MySQL en los pasos anteriores)
  
  4- database_user: (Usuario que has creado en la base de datos, por defecto es root)
  
  5- database_password: (Contraseña del usuario creado en la base de datos, por defecto es null)
  
  Todos los demás parámetros siguientes hasta "directorio_styles_public", oprimir Enter para tomar valores por defecto.
 
  6- site_url: localhost
  
  7- help_url: localhost
  
  Todos los demás parámetros siguientes, oprimir Enter para tomar valores por defecto.
  
Una vez terminada la instalación, si en el apartado anterior de configuración, no haz cargado un backup de la base de datos de MercadoFree, entonces es necesario generar la estructura de tablas, para ello, ejecutamos el siguiente comando en la terminal:

<pre>
php app/console doctrine:schema:create
</pre>

Una vez terminado, accedemos a la aplicación, mediante la siguiente URL en el navegador: http://localhost/mercadofree/web/app_dev.php

Debería aparecer la página de inicio de la web.


All libraries and bundles included in the Symfony Standard Edition are
released under the MIT or BSD license.

Enjoy!

[1]:  https://symfony.com/doc/2.8/setup.html
[6]:  https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html
[7]:  https://symfony.com/doc/2.8/doctrine.html
[8]:  https://symfony.com/doc/2.8/templating.html
[9]:  https://symfony.com/doc/2.8/security.html
[10]: https://symfony.com/doc/2.8/email.html
[11]: https://symfony.com/doc/2.8/logging.html
[12]: https://symfony.com/doc/2.8/assetic/asset_management.html
[13]: https://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html
