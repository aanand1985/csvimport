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

    defined('MOODLE_INTERNAL') || die();

    /**
     * The local plugin class
     */
    class local_csvimport_plugin
    {

        /*
         * Class constants
         */

        /**
         * @const string    Reduce chance of typos.
         */
        const PLUGIN_NAME                 = 'local_csvimport';

        /**
         * @const int       Max size of upload file.
         */
        const MAXFILESIZE                 = 51200;
    } // class
