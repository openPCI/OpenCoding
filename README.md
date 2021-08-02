# OpenCoding
Human coding of test taker responses to PCI and QTI items. Still work-in-progress. But does the basic tasks of importing responses, managing coders, traing, coding, flagging and handling flags, double coding, autocoding etc.

Integrates PCI's in the coding application.

## Howto
Create a database OpenCoding. Import the tables from opencoding.sql (using phpmyadmin).

Create af file called .htdatabase in a "secrets" folder. Fill in information about your database and user: localhost,opencoding,password,opencoding

Log in using admin user, no password. Change the password.

Go to OpenCoding Admin

Create a project.

Import data from csv-files. 

Go to Administer Tests. Upload images, write descriptions and rubrics, define items, and assign tasktypes to tasks. 

Create your own tasktypes, if you miss some. Please share. Twig-templates are used, so you can include variables and iterate over them.

Invite coders, train them. Manage their work. 

## Translate and contribute
Translate the sysem into your language using [poedit](https://poedit.net/). Share your translation by comitting to github (or send it to me).

## Help
Don't hessitate to reach out to [Jeppe Bundsgaard](mailto:jebu@edu.au.dk) for help or introduction to the system.
