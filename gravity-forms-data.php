<?php
/*
Plugin Name: Gravity Forms Data 
Plugin URI: http://www.voltampmedia.com/
Description: Get <a href="http://katz.si/gravityforms" rel="nofollow">Gravity Forms</a> from the Database for display, analysis, etc.
Author: Eric Cope
Version: 0.1
Author URI: http://www.voltampmedia.com

Copyright 2013 Voltamp Media, Inc.
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

register_activation_hook( __FILE__, array('GFData', 'init'));
add_action( 'init', array('GFData', 'enable'));

class GFData {

    function enable(){
        add_action('admin_menu', array('GFData','menu'));
    }

    function init() {
        add_option('gfdata_form_id', '');
    }

    function settings() {
        register_setting( 'gfdata_settings', 'gfdata_form_id');
    }

    function menu(){
        add_menu_page('GFData', 'GFData Settings', 'administrator', 'gravity-forms-data', array('GFData', 'config') );
        add_action('admin_init', array('GFData', 'settings'));
    }

    function config() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'gfdata_settings' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <td scope="row">Gravity Form</td>
                    <td>
                        <select id="" name="gfdata_form_id">
                            <option value="">  Select a Form  </option>
                            <?php
                                /**
                                 *  The magic to get all of the Gravity Forms
                                 */
                                $forms = RGFormsModel::get_forms(1, "title");
                                foreach($forms as $form) { ?>
                                    <option value="<?php echo absint($form->id) ?>" 
                                        <?php if(get_option('gfdata_form_id') == absint($form->id)) {
                                        echo ' selected="selected" '; 
                                        }?>
                                    >
                                        <?php echo esc_html($form->title) ?>
                                    </option>
                                <?php }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
        <?php 
    }

    function get_data($gfdata_form_id = null)
    {
        if(is_null($gfdata_form_id)) {
            $gfdata_form_id = get_option('gfdata_form_id');
        }

        $form = RGFormsModel::get_form_meta($gfdata_form_id);

        /** 
         * we return this
         */
        $gravity_fields = $form['fields'];

        $gfdata_fields = array();
        foreach($form['fields'] as $field) {
            if(!is_null($field['label'])) {
                $gfdata_fields[$field['id']] = $field['label'];
            }
        }
        $gravity_leads = RGFormsModel::get_leads($gfdata_form_id);

        //var_dump($gravity_leads);
        $gfdata = array();
        foreach($gravity_leads as $lead) {
            $temp = array();
            foreach($gfdata_fields as $id => $field) {
                if(array_key_exists($id,$lead)) {
                    $temp[$id] = $lead[$id];
                }
            }
            $gfdata[] = $temp;
        }
        unset($temp);

        return array(
            'gravity_fields' => $gravity_fields,
            'gfdata_fields'  => $gfdata_fields,
            'gravity_leads'  => $gravity_leads,
            'gfdata'         => $gfdata,
        );
    }
}
