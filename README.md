MercadoFree marketplace platform (Backend for pagofree)
========================

Welcome to the code repository of the MercadoFree marketplace platform. This platform is built on Symfony 2.8.52, using almost all the main Symfony components in its standard version (Symfony Standard Edition).

Local installation with Windows 10
--------------

To install the application locally for development, with Windows 10 operating system, the following steps must be followed:

  * Install XAMPP 7.2.27 (No other, just this one, to get PHP 7.2 version)

  * Once XAMPP is installed and Apache and MySQL started, open the terminal and go to the C: \ xampp \ htdocs directory, and execute the following command:

  <pre>
  git clone https://github.com/necobm/mercadofree.git mercadofree
  </pre>
  
  This downloads the repository and copies it to the C: \ xampp \ htdocs \ mercadofree directory.
  
Actions to take before installing vendors
--------------  

Before installing the vendors with composer, you must modify the following settings in the file "C: \ xampp \ php \ php.ini"

1- memory_limit = -1

2- max_execution_time = 120

Restart Apache using the XAMPP Control Panel

Loading the database locally
--------------  

First it is necessary to create a database for the application. To do this, enter the database through phpMyAdmin (http: // localhost / phpmyadmin /) or from your preferred database client.

Once the database and the user associated with said database have been created, you could load into said database the script corresponding to a recent backup with all the MercadoFree data and table structure, or leave it blank to generate the structure of tables later.

Installing vendors with Composer
--------------  
  
The next step would be to install the symfony vendors necessary for the project, for this we position ourselves in the directory C: \ xampp \ htdocs \ mercadofree and execute the following command:

  <pre>
  composer install
  </pre>
  
  This downloads and installs all the vendors, and at the end, it will ask us for the following configuration parameters, for the parameters.yml file:
  
  1- database_host: localhost
  
  2- database_port: (Enter, to take default value)
  
  3- database_name: (Name of the database that you have created in MySQL in the previous steps)
  
  4- database_user: (User that you have created in the database, by default it is root)
  
  5- database_password: (Password of the user created in the database, by default it is null)
  
  All other following parameters up to "public_styles_directory", press Enter to take default values.
 
  6- site_url: localhost
  
  7- help_url: localhost
  
  All other parameters below, press Enter to take default values.
  
Once the installation is finished, if in the previous configuration section, you have not loaded a backup of the MercadoFree database, then it is necessary to generate the table structure, for this, we execute the following command in the terminal:

<pre>
php app / console doctrine: schema: create
</pre>

Once finished, we access the application through the following URL in the browser: http: //localhost/mercadofree/web/app_dev.php

The web home page should appear.


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
