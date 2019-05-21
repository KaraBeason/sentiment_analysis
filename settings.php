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
 *  Settings for block_sentimentanalysis
 *
 * @author      Kara Beason <beasonke@appstate.edu>
 * @copyright   (c) 2019 Appalachian State Universtiy, Boone, NC
 * @license     GNU General Public License version 3
 * @package     block_sentimentanalysis
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $field = "paths";
    $pluginname = "block_sentimentanalysis";
    $adminSetting = new admin_setting_heading(
        "{$pluginname}/{$field}",
        get_string("{$field}",  $pluginname),
        get_string("{$field}desc", $pluginname));
    $settings->add($adminSetting);
    unset($adminSetting);

    $field = "pythonpath";
    $adminSetting = new admin_setting_configtext(
        "{$pluginname}/{$field}",
        get_string("{$field}",  $pluginname),
        get_string("{$field}desc", $pluginname),
        get_string("{$field}default", $pluginname),
        PARAM_RAW );
    $settings->add($adminSetting);
    unset($adminSetting);
}