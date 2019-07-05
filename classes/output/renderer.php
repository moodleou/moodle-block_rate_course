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
 *
 * @copyright  2019 Pierre Duverneix <pierre.duverneix@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_rate_course\output;
defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {

    /**
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_rating(\templatable $output) {
        $data = $output->export_for_template($this);
        return $this->render_from_template('block_rate_course/rating', $data);
    }

    /**
     *
     * @param \templatable $output
     * @return string|boolean
     */
    public function render_rateform(\templatable $output) {
        $data = $output->export_for_template($this);
        return $this->render_from_template('block_rate_course/rate-form', $data);
    }

}
