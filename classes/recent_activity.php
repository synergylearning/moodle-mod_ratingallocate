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
 * The mod_ratingallocate "recent activity" block helper.
 *
 * @package    mod_ratingallocate
 * @copyright  2016 Janek Lasocki-Biczysko <j.lasocki-biczysko@intrallect.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_ratingallocate;

class recent_activity {

    private static $eventnames = [
        '\mod_ratingallocate\event\allocation_published'
    ];

    /**
     * @return \logstore_standard\log\store
     */
    public static function get_event_store() {
        $manager = get_log_manager(true);
        $stores = $manager->get_readers('\logstore_standard\log\store');
        return $stores['logstore_standard'];
    }

    /**
     * @return \core\event\base[]
     */
    public static function get_recent_activity_events($course, $timestart) {

        global $DB;

        list($eventnamewhere, $params) = $DB->get_in_or_equal(self::$eventnames, SQL_PARAMS_NAMED);

        $selectwhere = "courseid = :courseid AND timecreated > :timestart AND eventname $eventnamewhere";
        $params['courseid'] = $course->id;
        $params['timestart'] = $timestart;

        $store = self::get_event_store();
        $events = $store->get_events_select($selectwhere, $params, 'timecreated', 0, 20);

        return $events;
    }

    /**
     * @param \core\event\base[] $events
     */
    public static function get_activity_names($events) {

        $modids = [];
        foreach ($events as $event) {
            $modids[] = $event->objectid;
        }

        global $DB;
        list($idinoreq, $params) = $DB->get_in_or_equal($modids);
        $ratingsallocate = $DB->get_records_select_menu('ratingallocate', "id $idinoreq", $params, '', 'id,name');

        return $ratingsallocate;
    }
}