# FRET community website
This document describes how the community website is set-up and managed. The website should serve as an information hub for the community. In addition, there had to be a registration process that allowed people to store and update their information. The list of registered participants can now be used for a mailing list. In addition, the problem had to be solved how to perform votes for the advisory board, and a comprehensive list of software packages should be provided.
## Basic setup

### IT infrastructure
We chose to use a self-hosted WordPress website for the FRET community. Currently (11/2019), this website is hosted at HHU Düsseldorf, Germany, and the hosting infrastructure is in responsibility of Mathias Fröscher. The domain of the website is  currently www.fret.community, which redirects to https://www3.hhu.de/fluorescence-science/.
FTP access to the server is available under the address sftp://mpcwww@www2.hhu.de e.g. through the app FileZilla. Inquire to Mathias Fröscher for the password. Use the FTP to edit code in the WordPress installation or to manually install plugins.
#### DNS redirect
To change the displayed URL from h"www3.hhu.de/fluorescence-science/" to "https://www.fret.community", the Wordpress AND Site-URL under Settings->General has to be changed to "https://www.fret.community".
### WordPress theme
We have chosen the theme "Materialis" for the website as it offers a modern and customizable look. In principle, the theme can be changed at any time, although there are some important code additions in the `functions.php` file that need to be copied over to the new theme. Most of the design was done in the "Customizer" and is pretty self-explanatory. Some custom CSS code has been added related to the formatting of some text passages, found at Customizer->General->Additional CSS.
### Pages and Posts
WordPress offers two types of sites, pages and posts. Pages are used for permanent sites, such as the homepage, software list etc. Posts are handled as "news" in the website and will be listed on the homepage and the separate "News" page. Use posts for announcements, updates, or small articles. Posts can additionally be assigned to categories that describe the content.
### Wrapping text around images
Inline images only work if you use the "classic" block in the wordpress editor, found under "Formatting -> Classic" when inserting a new content block. (Support should also be there for the normal blocks, but this results in the image being placed above the text in a separate <div>.)
### Errors...
Using the Materialis theme, there occured an error related to the JavaScript version used by Wordpress and the Masonry JavaScript package that arranges boxes/windwos (such as posts or images). I tried to figure out what the error was and ended up just suppressing it by catching it and exiting the function. The error was that sometimes a window was passed to the function `jquery.masonry.min.js` found in `/wp_includes/js/jquery/`that did not contain a `Masonry` attribute (of which erroneously properties were requested afterwards, leading to the error). The code of the first lines was originally:

```
! function (a) {
    "use strict";
    var b = a.Masonry;
    b.prototype._remapV2Options = function () {
```

To catch the case that `b` is undefined, the following check was added after the declaration of `var b = a.Masonry;`:

```
    if (typeof b == 'undefined') {
        return;
    };
```
This "fixed" the error (well, at least it suppresses the warning in the front end).

## Membership management
### Ultimate Member (UM) plugin
For the registration and listing of member, we used the plugin "Ultimate Member", which offered most of the functionality we required.
### Custom meta fields
In the basic installation, UM only offers only standard fields such as name, email, etc. Fortunately, UM allows for custom fields to be defined in the registration form. These fields were e.g. the current career status, affiliation etc. Some conditional logic was required for the registration form to hide and show some fields depending on the choice of the career status. As an example, the category "Early stage researcher" should provide an additional choice between Master, PhD, PostDoc, which should not be given for senior researchers. This was all possible in the registration form.
### Account review
It is suggested that account review is enabled to filter out spam registrations. For this, the user role "Subscriber" (the default role) has to be modified to require manual account review.
### Updating of profile information
UM allows the user to modify their basic profile information on the website, such as email and name. The custom meta fields had to be manually added to the profile form, such that they could be updated on demand. UM distinguishes between "Account" and "Profile". If the user clicks on "Account", they can update the basic information (name and email), whereas "Profile" allows to change the meta data.
#### Showing the meta information in the profile
To display custom meta fields in the profile, they have to be added in the profile form in UM. In the bottom right, you will have the option to add custom meta fields.
### Displaying a member directory
UM offers an internal way to display an overview of the community members in a grid with only the basic information. It was requested that instead the members be given in a list with affiliation and career status displayed. After much search, we found the "Dynamic User Directory" plugin which provided exactly what we needed. There exists a paid extension to this plugin which would allow smart filtering of users based on meta fields. For the future, this might be worth considering.
#### Update: 11/2019
The UM plugin now also displays a list view by default. Filtering by custom meta data also works by default, so in essence the "Dynamic User Directory" is not needed anymore. Custom filters are displayed as dropdown menus, whereby the default data placeholder is used to label the dropdown. This field is based on the "label" attribute of the field in the database, which has to be set in the registration form.
### Accessing user meta data in the backend
Custom user meta data is, by default, not accessible in the UM backend. Two plugins are used: "User Meta Display" to display and modify the user meta data for individual users, and "Export User Data" to export the data as excel or csv sheet for all users. In addition, through some code modifications in the `functions.php` file, it is possible to add the user meta data to the user table (Users->All Users). See the associated file for the additions that have to be performed.
### Issues with custom user meta data
One major issue arises when checkboxes are used to store custom user meta data. UM will store these values as an array with a single element, which in turn produces problems when these fields are accessed without "imploding" the array to convert it into a string. Several modifications had to be performed to the code of different plugins to be able to access user meta data at different stages.
In php, the following code snippet will convert an array to a comma-separated string of its elements:

```
<?php
$array = custom_meta_field;
if is_array($array) {
	$array = implode(', ',$array);
}
```

This fix had to be applied to the following functions:
- For the display of the user meta data in the UM user table. See the modifications to the `functions.php` file of the theme.
- UM offers the possibility to display user meta data of logged in users using a shortcode `{usermeta:meta_key}`. See the modifications to the `um-short-functions.php` file of UM.
- Contact Forms 7 offers the ability to set default values for input fields based on the user meta data. This function is used to modify a form behind the scenes based on the current career status of the registered user (e.g. for voting only for candidates in their category). See the modifications to the `form-tag.php` file of Contact Form 7.
## Software list
The software list provided a challenge because we required a searchable list that provided key information at a glance, but also offered more detailed information on request. New software packages can be registered through a contact form. We choose the plugin TablePress to provide a searchable dynamic table. To give the option of only showing detailed information on demand, the extension "Row Details" for TablePress provided exactly what we needed, as it allows to designate one column via shortcode as collapsible, i.e. it is only shown if a plus sign is clicked.
## Election of the editorial board
The election of the editorial board required the setup of a contact form that allows members to register as a candidate and a possibility to vote for the candidates in the members' respective category, with a limit of one vote.
### Candidate registration
If the candidate registers himself, the form should transmit the information stored in the "current_career_status" meta data. To access this information, we use CF7's option to set default values for input fields and set those as read-only. The information from the field is then transmitted with the contact form. In addition, the option is provided to register another participant.
### Voting
Voting is also performed over a CF7 contact form. As for the candidate registration, the default value options are used to determine what career status a member has, and different groups in the contact form are hidden and shown based on the career status. To store the votes, we use the Contact Forms Database plugin ([CFDB](https://cfdbplugin.com)), which allows a simple export to excel.
#### Preventing duplicate votes
Prevention of duplicate votes proved challenging. We found an extension for the CFDB plugin that allows the prevention of duplicate votes through a custom filter. This required the plugin ["Add Shortcodes Actions And Filters"](https://wordpress.org/plugins/add-actions-and-filters/). We followed the tutorial given [here](https://cfdbplugin.com/?page_id=904) to implement the custom filter for the contact form (code given in appendix below). The form checks if the "user-login" of the voting user is already in the database and rejects the form submission.

# Appendix
## Modifications to theme (functions.php)
```
<?php
/**
* following code sets up the display of custom meta data in the user table
* see: https://gist.github.com/magnific0/29c32c7dabc89ab9cae5
*/
function mysite_custom_define() {
    $custom_meta_fields = array(); 
    $custom_meta_fields['current_career_status'] = 'Current career status';
    $custom_meta_fields['institution'] = 'Institution';
    $custom_meta_fields['institute'] = 'Institute';
    $custom_meta_fields['city'] = 'City';
    $custom_meta_fields['state'] = 'State';
    $custom_meta_fields['country'] = 'Country';
    return $custom_meta_fields;
}

function mysite_columns($defaults) {
    $meta_number = 0;
    $custom_meta_fields = mysite_custom_define();
    foreach($custom_meta_fields as $meta_field_name => $meta_disp_name) {
        $meta_number++;
        $defaults[('mysite-usercolumn-'.$meta_number.
            '')] = __($meta_disp_name, 'user-column');
    }
    return $defaults;
}

function mysite_custom_columns($value, $column_name, $id) {
    $meta_number = 0;
    $custom_meta_fields = mysite_custom_define();
    foreach($custom_meta_fields as $meta_field_name => $meta_disp_name) {
        $meta_number++;
        if ($column_name == ('mysite-usercolumn-'.$meta_number.
                '')) {
            $value = get_the_author_meta($meta_field_name, $id);
            if (is_array($value)){
                $value = $value[0];
            }
            return $value;
        }
    }
}

function mysite_save_extra_profile_fields($user_id) {

    if (!current_user_can('edit_user', $user_id))
        return false;

    $meta_number = 0;
    $custom_meta_fields = mysite_custom_define();
    foreach($custom_meta_fields as $meta_field_name => $meta_disp_name) {
        $meta_number++;
        update_user_meta($user_id, $meta_field_name, $_POST[$meta_field_name]);
    }
}

function mysite_show_extra_profile_fields($user) {
    print('<h3>Extra profile information</h3>');

    print('<table class="form-table">');

    $meta_number = 0;
    $custom_meta_fields = mysite_custom_define();
    foreach($custom_meta_fields as $meta_field_name => $meta_disp_name) {
        $meta_number++;
        $value = esc_attr(get_the_author_meta($meta_field_name, $user->ID));
        if(is_array($value)) {
            $value = $value[0];
        }
        print('<tr>');
        print('<th><label for="'.$meta_field_name.
            '">'.$meta_disp_name.
            '</label></th>');
        print('<td>');
        print('<input type="text" name="'.$meta_field_name.
            '" id="'.$meta_field_name.
            '" value="'.$value.
            '" class="regular-text" /><br />');
        print('<span class="description"></span>');
        print('</td>');
        print('</tr>');
    }
    print('</table>');
}

add_action('show_user_profile', 'mysite_show_extra_profile_fields');
add_action('edit_user_profile', 'mysite_show_extra_profile_fields');
add_action('personal_options_update', 'mysite_save_extra_profile_fields');
add_action('edit_user_profile_update', 'mysite_save_extra_profile_fields');
add_action('manage_users_custom_column', 'mysite_custom_columns', 15, 3);
add_filter('manage_users_columns', 'mysite_columns', 15, 1);
```

## Modifications to UM plugin (um-short-functions.php)
Replace lines 209-215 of um-short-functions.php with the following code to 
correctly display array user meta fields using shortcode {usermeta:meta:key}.

```
    // Support for all usermeta keys
	if ( ! empty( $matches[1] ) && is_array( $matches[1] ) ) {
		foreach ( $matches[1] as $match ) {
			$strip_key = str_replace( 'usermeta:', '', $match );
			
			$value = um_user( $strip_key );
			if(is_array($value)) {
				$value = implode(', ', $value);
			}

			$content = str_replace( '{' . $match . '}', $value, $content );
		}
	}
```

## Modifications to Contact Form 7 (form-tag.php)
Add this code in line 232 to form-tag.php in Contact Form 7 to use custom user fields that are saved as arrays as default values in CF7.

```
if (is_array($user_prop)) {
    $user_prop = implode(', ',$user_prop);
}
```
## Custom filter code to prevent duplicate voting
```
/**
 * @param $formName string
 * @param $fieldName string
 * @param $fieldValue string
 * @return bool
 */
function is_already_submitted($formName, $fieldName, $fieldValue) {
    require_once(ABSPATH . 'wp-content/plugins/contact-form-7-to-database-extension/CFDBFormIterator.php');
    $exp = new CFDBFormIterator();
    $atts = array();
    $atts['show'] = $fieldName;
    $atts['filter'] = "$fieldName=$fieldValue";
    $atts['unbuffered'] = 'true';
    $exp->export($formName, $atts);
    $found = false;
    while ($row = $exp->nextRow()) {
        $found = true;
    }
    return $found;
}
 
/**
 * @param $result WPCF7_Validation
 * @param $tag array
 * @return WPCF7_Validation
 */
function my_validate_email($result, $tag) {
    $formName = 'Voting'; // Change to name of the form containing this field
    $fieldName = 'user-login'; // Change to your form's unique field name
    $errorMessage = 'You already voted!'; // Change to your error message
    $name = $tag['name'];
    if ($name == $fieldName) {
        if (is_already_submitted($formName, $fieldName, $_POST[$name])) {
            $result->invalidate($tag, $errorMessage);
        }
    }
    return $result;
}
 
add_filter('wpcf7_validate_text*', 'my_validate_email', 10, 2);
```
