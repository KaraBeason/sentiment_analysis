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
 * block_sentimentanalysis
 *
 * @author      Kara Beason <beasonke@appstate.edu>
 * @copyright   (c) 2019 Appalachian State Universtiy, Boone, NC
 * @license     GNU General Public License version 3
 * @package     block_sentimentanalysis
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_sentimentanalysis';  // Recommended since 2.0.2 (MDL-26035). Required since 3.0 (MDL-48494)
$plugin->version = 2018120302.00;  // YYYYMMDDHH (year, month, day, 24-hr time)
$plugin->requires = 2010112400; // YYYYMMDDHH (This is the release version for Moodle 2.0)
$plugin->release    = "1.0.0 (Build 2019051600)";
$plugin->cron       = 0;
$plugin->maturity   = MATURITY_BETA;