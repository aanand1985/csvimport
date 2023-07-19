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

    defined('MOODLE_INTERNAL') || die;

    if ($hassiteconfig) {
        $ADMIN->add('users', new admin_externalpage('sendrandomemail', get_string('send_random_email','local_csvimport'), "$CFG->wwwroot/local/csvimport/import.php"));
    }
