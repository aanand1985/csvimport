<?php
    // This file is part of Moodle - http://moodle.org/
    //
    // Moodle is free software: you can redistribute it and/or modify
    // it under the terms of the GNU General Public License as published by
    // the Free Software Foundation, either version 3 of the License, or
    // (at your option) any later version.
    //
    // Moodle is distributed in the hope that it will be useful,
    // but WITHOUT ANY WARRANTY; without even the implied warranty of
    // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    // GNU General Public License for more details.
    //
    // You should have received a copy of the GNU General Public License
    // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

    /**
     * local_csvimport
     *
     * This plugin will import users from a csv file.
     *
     * @author      Anuraj Anand
     * @license     GNU General Public License version 3
     * @copyright   2023 Anuraj Anand
     * @package     local_csvimport
     */

    require_once(__DIR__ . '/../../config.php');
    require_once(__DIR__ . '/lib.php');
    require_once(__DIR__ . '/import_form.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/csvlib.class.php');

    $userid = optional_param('userid', '', PARAM_INT);
    $action = optional_param('action', '', PARAM_TEXT);

    // Require login
    require_login();

    // check permission to access the plugin
    $systemcontext = context_system::instance();
    require_capability('moodle/site:uploadusers', $systemcontext);

    // Get the return URL for this and related pages.
    $returnurl = new moodle_url('/local/csvimport/import.php');

    $page_head_title = get_string('import_title',  local_csvimport_plugin::PLUGIN_NAME);
    
    admin_externalpage_setup('sendrandomemail');

    // Set some options for the filepicker
    $file_picker_options = array(
		'accepted_types' => array('.csv'),
    );
    $user_context = context_user::instance($USER->id);

    $formdata = null;
    $mform    = new local_csvimport_index_form($PAGE->url->out(), array('options' => $file_picker_options));

    if ($formdata = $mform->is_cancelled()) {
        get_file_storage()->delete_area_files($user_context->id, 'user', 'draft', file_get_submitted_draft_itemid('userfile'));
        redirect($returnurl);
    
    } elseif ($mform->is_submitted() && $formdata = $mform->get_data()) {

        //$area_files = get_file_storage()->get_area_files($user_context->id, 'user', 'draft', $formdata->{'userfile'}, null, false);
            
        // Open and fetch the file contents using moodle csv library.
        $csvimportid = csv_import_reader::get_new_iid('uploaduser');
        $csvimportreader = new csv_import_reader($csvimportid, 'uploaduser');

        $content = $mform->get_file_content('userfile');

        $countusers = $csvimportreader->load_csv_content($content, 'UTF-8', ',');

        $csvloaderror = $csvimportreader->get_error();
        if (!is_null($csvloaderror)) {
            print_error('csvloaderror', '', $returnurl, $csvloaderror);
        } elseif (!is_null($countusers)){
            $csvimportreader->init();
            while ($line = $csvimportreader->next()) {
                $newuser = new stdClass();
                $newuser->firstname     = $line[0];
                $newuser->lastname      = $line[1];
                $newuser->email         = $line[2];
                $DB->insert_record('import_users', $newuser);
            }

            // Clean up the file area
            get_file_storage()->delete_area_files($user_context->id, 'user', 'draft', $formdata->{'userfile'});

        }
    } elseif (!empty($action) && !empty($userid)){
        // Email to user.
        $subject = get_string('subject', local_csvimport_plugin::PLUGIN_NAME);
        $body    = get_string('message', local_csvimport_plugin::PLUGIN_NAME)."\n\n";
        $from->firstname = $USER->firstname;
        $from->lastname  = $USER->lastname;
        $from->email     = "<$USER->email>";
        // email_to_user($user,$from,$subject,$body);

        $obj = new stdClass();
        $obj->id = $userid;
        $obj->timesent = time();
        $DB->update_record('import_users', $obj, true);
    }

    // Display the form with filepicker
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('send_random_email', local_csvimport_plugin::PLUGIN_NAME));

    echo $OUTPUT->container_start();
    $mform->display();
    echo $OUTPUT->container_end();
    // Form End

    // Display imported users
    echo $OUTPUT->container_start();
    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo \html_writer::tag('p', '');
    echo $OUTPUT->heading(get_string('imported_users', local_csvimport_plugin::PLUGIN_NAME));

    $importedusers = $DB->get_records('import_users');

    if (empty($importedusers)) {
        echo \html_writer::tag('p', get_string('no_users', local_csvimport_plugin::PLUGIN_NAME));
    } else {
        $columns = array(
            'id' => get_string('id', local_csvimport_plugin::PLUGIN_NAME),
            'name' => get_string('name'),
            'email' => get_string('email'),
            'sendmail' => get_string('send_random_email', local_csvimport_plugin::PLUGIN_NAME)
        );

        // Display table - list of users.
        $table = new \flexible_table('importedusers');
        $table->set_attribute('class', 'table flexible');
        $table->define_columns(array_keys($columns));
        $table->define_headers(array_values($columns));
        $table->define_baseurl($PAGE->url);
        $table->setup();
        $i=1;
        foreach ($importedusers as $user) {
            $name = $user->firstname .' '. $user->lastname;
            if(empty($user->timesent)){
                $mailurl = new moodle_url('/local/csvimport/import.php', array(
                    'action' => 'sendmail',
                    'userid' => $user->id
                ));
                $maillink = $OUTPUT->action_icon($mailurl, new pix_icon('t/go', 
                get_string('send_email_to', local_csvimport_plugin::PLUGIN_NAME).$name));
            } else {
                $maillink = get_string('mail_sent_on', local_csvimport_plugin::PLUGIN_NAME).date('d/m/Y H:i:s', $user->timesent);
            }

            $table->add_data(array(
                $i,
                $name,
                $user->email,
                $maillink
            ));
            $i++;
        }

        $table->finish_output();
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();