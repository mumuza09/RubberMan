<?php
/**
 * Theme Customizer Functions
 *
 * @package Pride Themes
 * @subpackage Shopping Store
 * @since Shopping Store 1.0
 */
/********************* SHOPPING STORE CUSTOMIZER SANITIZE FUNCTIONS *******************************/
function shopping_store_checkbox_integer($input)
{
    return ((isset($input) && true == $input) ? true : false);
}

function shopping_store_sanitize_select($input, $setting)
{

    // Ensure input is a slug.
    $input = sanitize_key($input);

    // Get list of choices from the control associated with the setting.
    $choices = $setting->manager->get_control($setting->id)->choices;

    // If the input is a valid key, return it; otherwise, return the default.
    return (array_key_exists($input, $choices) ? $input : $setting->default);

}

function shopping_store_sanitize_page($input)
{
    if (get_post($input)) {
        return absint($input);
    } else {
        return '';
    }
}

function shopping_store_reset_alls($input)
{
    if ($input == 1) {
        delete_option('shopping_store_theme_options');
        $input = 0;
        return $input;
    } else {
        return '';
    }
}
