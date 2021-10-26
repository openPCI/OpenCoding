# OpenCoding
Human coding of test taker responses to PCI and QTI items. Importing responses, creating task types, managing projects, coders, traning, coding, flagging and handling flags, double coding, autocoding etc. Fully localizable.

Integrates PCI's in the coding application.

## Howto
0. Get a webserver with PHP and MySql up and running. 
	* For example you can install a LAMP, WAMP or MAMP-pacakage, see [Wikipedia's List of Apache–MySQL–PHP packages](https://en.wikipedia.org/wiki/List_of_Apache%E2%80%93MySQL%E2%80%93PHP_packages).
	* Use [PhpMyAdmin](https://www.phpmyadmin.net/) for database administration

1. Upload all files in this repository to the root folder of your webserver (e.g. c:\wamp\www, /var/www/html)
	
2. Create a database called opencoding, give a user access to this database. Import the tables from the file opencoding.sql (using phpmyadmin). And import tasktypes into the tasktypes-table from the file tasktypes.sql

3. Create af file called .htdatabase in a "secrets" folder. Fill in information about your host, user, password and database: localhost,opencoding,password,opencoding

Log in using admin user, no password. Change the password.

4. Go to OpenCoding Admin

Create a project.

5. Go to Project Admin.

Import data from csv-files. 

Go to Administer Tests: Upload images, write descriptions and rubrics, define items, and assign tasktypes to tasks. 

- Create your own tasktypes, if you miss some. Please share. Twig-templates are used, so you can include variables and iterate over them.

Go to users and give yourself Codingadmin permissions

6. Go to Coding Admin

Invite coders (including yourself). Code some responses and select items for training use. 

Train coders. Manage their work, resolve their flagged responses.

## Translate and contribute
Translate the sysem into your language using [poedit](https://poedit.net/). Share your translation by comitting to github (or send it to me).

## Help
Don't hessitate to reach out to [Jeppe Bundsgaard](mailto:jebu@edu.au.dk) for help or introduction to the system.
