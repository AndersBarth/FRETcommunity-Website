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
            return get_the_author_meta($meta_field_name, $id);
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