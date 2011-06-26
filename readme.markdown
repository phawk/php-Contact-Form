Simple PHP Contact Form Library
===============================

Ever had to make a quick contact form on PHP site that's not using a framework or doesn't have contact form stuff built in? Well I got fed up of doing just that. The last time I had to make a PHP contact form I put a little bit more effort into making it reusable and bam we have this little library.

Installation
------------

* Paste the files into your site
* Edit your database settings, email address and subject line in the libraries/contact_form.php file.
* Run the enquiries.sql create table stuff in your database
* And use the example.php file to figure out how to integrate it into your setup.

Notes
-----

Feel free to extend and if you have any comments or improvements, do let me know. Passing the entire $_POST array may seem a little dodgey but there is some stuff to cleanup the code and prevent MySQL injection.