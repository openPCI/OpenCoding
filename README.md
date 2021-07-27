# OpenCoding
Human coding of test taker responses to PCI and QTI items. Still work-in-progress. But does the basic tasks of importing responses, managing coders, traing, coding, flagging and handling flags, double coding, autocoding etc.

Integrates PCI's in the coding application.

## Howto
Create a database OpenCoding. Import the tables from opencoding.sql (using phpmyadmin).

Create af file called .htdatabase in a "secrets" folder. Fill in information about your database and user: localhost,opencoding,password,opencoding

Log in using admin user, no password. Change the password.

Manually create a project (using phpmyadmin).

Import data from csv-files. 

Manage the project. 

Assign tasktypes to tasks. 

Create your own tasktypes, if you miss some. Please share. Twig-templates are used, so you can include variables and iterate over them.

Invite coders, train them. Manage their work. 

Don't hessitate to reach out to jebu@edu.au.dk for help or introduction to the system.
