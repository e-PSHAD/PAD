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
 * Local extensions for PAD+ which do not fit in the theme:
 *
 * - global navigation extension
 * - custom renderer
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_padplusextensions';// Full name of the plugin (used for diagnostics).
$plugin->version = 2022050300; // This is the version of the plugin.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'v1.0.0';

$plugin->requires = 2021051700.00; // This is the version of Moodle this plugin requires.
