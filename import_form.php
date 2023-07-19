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

    require_once($CFG->libdir.'/formslib.php');

    /**
     * This plugin will import users from a csv file.
     */
    class local_csvimport_index_form extends moodleform {

        /**
         * Define the form's contents
         */
        public function definition()
        {
            // File picker
            $this->_form->addElement('header', 'identity', get_string('import_title', local_csvimport_plugin::PLUGIN_NAME));

            $this->_form->addElement('filepicker', 'userfile', null, null, $this->_customdata['options']);
            $this->_form->addRule('userfile', null, 'required');

            $this->add_action_buttons(true, get_string('csv_import', local_csvimport_plugin::PLUGIN_NAME));

        }


        /**
         * Validate the submitted form data
         */
        public function validation($data, $files)
        {
            global $USER;

            $result = array();

            // The file should be in the user's draft area
            $area_files = get_file_storage()->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft');
            $import_file = array_shift($area_files);
            if (null == $import_file) {
                $result['filepicker'] = get_string('no_files', local_csvimport_plugin::PLUGIN_NAME);
            }

            return $result;

        }

    }