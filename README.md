PHP Router
==========
Description
-----------
This is a simple PHP router that allows you to quickly create a website that is both dynamic and SEO friendly without a database. It also contains an easy way to create templated views.

Usage
-----
- Create a file in router/view to display your data that includes the header and footer templates
- Create a file in router/controller to create & control your data that includes your view
- Define the URL and which corresponding controller file to load in router/system/config.routes.php

Misc
----
- index.php contains convenient constants to access different folders and parts of the URI
- router/system/helper.php has a bunch of convenience functions I tend to use
