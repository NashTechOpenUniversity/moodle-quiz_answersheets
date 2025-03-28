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

namespace mod_oucontent\event;

/**
 * Export downloaded event. Only admins should download the export-all zips.
 *
 * @package mod_oucontent
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exportall_downloaded extends \core\event\base {
    #[\Override]
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'course';
    }

    #[\Override]
    public static function get_name() {
        return get_string('eventexportall_downloaded', 'mod_oucontent');
    }

    #[\Override]
    public function get_description() {
        return 'User ' . $this->userid . ' has downloaded SC documents export for course ' .
            $this->objectid;
    }

    #[\Override]
    public function get_url() {
        return new \moodle_url('/mod/oucontent/exportall.php');
    }
}
