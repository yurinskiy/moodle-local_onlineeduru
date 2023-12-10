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
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_onlineeduru\output;

defined('MOODLE_INTERNAL') || die();

use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use local_onlineeduru\helper;
use plugin_renderer_base;

/**
 * Renderer class for 'local_onlineeduru' component.
 *
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * @return string HTML to output.
     */
    public function courses_table($courses, bool $isManager = false ): string
    {

        $table = new html_table();
        $table->head  = [
            get_string('name'),
            get_string('url'),
            get_string('gis_courseid', 'local_onlineeduru'),
            'Дата отправки',
            get_string('actions'),
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $data = [];

        $index = 0;

        foreach ($courses as $course) {
            $passport = json_decode($course->request);

            // Name.
            $name = $passport->title;
            $namecell = new html_table_cell(s($name));
            $namecell->header = true;

            // Url.
            $url = $passport->external_url;
            $urlcell = new html_table_cell(s($url));

            $gis = $course->gis_courseid;
            $giscell = new html_table_cell(s($gis));

            $time = userdate($course->timerequest);
            $timecell = new html_table_cell(s($time));

            $links = '';
            // Action links.
            $viewurl = helper::get_view_passport_url($course->courseid);
            $viewlink = html_writer::link($viewurl, $this->pix_icon('t/hide', get_string('view')));
            $links .= ' ' . $viewlink;

            if ($isManager) {
                $editurl = helper::get_update_passport_url($course->courseid, helper::ACTION_UPDATE);
                $editlink = html_writer::link($editurl, $this->pix_icon('t/edit', get_string('edit')));
                $links .= ' ' . $editlink;
            }

            $editcell = new html_table_cell($links);

            $row = new html_table_row([
                $namecell,
                $urlcell,
                $giscell,
                $timecell,
                $editcell,
            ]);

            $data[] = $row;
            $index++;
        }
        $table->data = $data;
        return html_writer::table($table);
    }
}