gravity-forms-data
==================

Wordpress Plugin to Extract Data in Database Inserted by Gravity Forms. 

Gravity Forms inserts data into the Wordpress database, but to pull the data, one must know the form ID. 
This plugin allows the user to select the form in the Admin section, the pull the data associated with 
that form. Its a raw PHP class essentially, so you still need to manipulate the data within your theme, 
widget, etc.

Example:
In your theme file, use this code:

    $data = GFData::get_data();
    
    $gfdata_fields  = $data['gfdata_fields'];   // slightly processed fields for easier manipulation
    $gfdata         = $data['gfdata'];          // slightly processed leads data for easier manipulation
    $gravity_fields = $data['gravity_fields'];  // raw gravity fields data
    $gravity_leads  = $data['gravity_leads'];   // raw gravity leads data
    
Now, you can process the data as you wish.
