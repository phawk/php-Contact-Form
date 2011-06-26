<?php

// Include the contact class.
include 'libraries/contact_form.php';

// Instantiate the class and pass the post array
$contact = new Contact_form($_POST);

// Save the data to your database
$rowsaffected = $contact->save_data();

// If the data is saved email it and redirect
if($rowsaffected)
{
	$mailme = $contact->send_mail();
	header('Location: /contact/thanks');
}
else
{
	// There was a problem, show an error
	echo 'Sorry there was a problem, make sure you filled in all of the fields and please go back and try again.';
}

?>