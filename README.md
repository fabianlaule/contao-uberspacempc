UberspaceMPC
============

This extension for Contao Open Source CMS enables users to change their mailbox password in the frontend of the website. This extensionen was developed for the great hoster called [Uberspace](https://uberspace.de "Go to Uberspace.de").

###Installation
- You can use the composer for Contao to add this extension to your installation. Easily search for 'fabil/uberspacempc' and select the newest release of this extension. Please update the database after the installation!
- You also have the possibility to download the [newest release](https://github.com/fabil/uberspacempc/releases "Go to the releases"), extract and move it to the folder 'uberspacempc' (which you have to create) in system/modules. After that, please update your database!
 
###How can I use this extension?

In the backend, you can easily assign an existing mailbox account to one or more frontend users. As soon as the logged in frontend user visits the site with the embedded module, he finds a form where he can change the password for one of the assigned mailbox accounts.

###It's in development!
From time to time, I'll add more and more features and update it for newer contao releases. This extension only works properly with Contao 3 and higher.

Please remeber that you only can see the frontend module if you are logged in as a frontend user and at least one mailbox was assigned to this user. Also, this extension supports only the vmailmgr configuration of the german based hoster [Uberspace.de](https://uberspace.de "Go to Uberspace.de")
