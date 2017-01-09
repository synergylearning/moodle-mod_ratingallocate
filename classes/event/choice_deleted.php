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
 * The mod_ratingallocate choice_deleted.
 *
 * @package    mod_ratingallocate
 * @copyright  2017 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_ratingallocate\event;
defined('MOODLE_INTERNAL') || die();
/**
 * The mod_ratingallocate choice_deleted event class.
 *
 * @copyright 2017 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class choice_deleted extends \core\event\base {

    public static function trigger_from_choice($context, $choice){
        if ($choice instanceof \ratingallocate_choice) {
            $choice = $choice->dbrecord;
        }
        $evt = self::create(['context' => $context, 'objectid' => $choice->id]);
        $evt->add_record_snapshot('ratingallocate_choices', $choice);
        $evt->trigger();
    }
    
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'ratingallocate_choices';
    }
 
    public static function get_name() {
        return get_string('log_choice_deleted', 'mod_ratingallocate');
    }
 
    public function get_description() {
        return "The user with id '$this->userid' has deleted the choice with id '$this->objectid' in the Fair allocation ".
            "activity with cmid '$this->contextinstanceid'";
    }
 
    public function get_url() {
        return new \moodle_url('/mod/ratingallocate/view.php', array('id' => $this->contextinstanceid));
    }

    public static function get_objectid_mapping() {
        return array('db' => 'ratingallocate_choices', 'restore' => 'ratingallocate_choices');
    }

    public static function get_other_mapping() {
        return false;
    }
}
