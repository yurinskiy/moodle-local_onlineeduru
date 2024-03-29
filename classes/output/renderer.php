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
        global $OUTPUT;

        $table = new html_table();
        $table->head  = [
            'Наименование онлайн-курса',
            get_string('url'),
            get_string('gis_courseid', 'local_onlineeduru'),
            'Дата отправки',
            'Статус',
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

            $statuscell = new html_table_cell($this->statusCell($course));

            $links = '';
            // Action links.
            $viewurl = helper::get_view_passport_url($course->courseid);
            $viewlink = html_writer::link($viewurl, $this->pix_icon('t/hide', get_string('view')));
            $links .= ' ' . $viewlink;

            if ($course->status != 200 && $isManager) {
                $editurl = helper::get_update_passport_url($course->courseid);
                $editlink = html_writer::link($editurl, $this->pix_icon('i/edit', get_string('edit')));
                $links .= ' ' . $editlink;
            }

            if ($course->status != 200 && $isManager) {
                $resendurl = helper::get_resend_url($course->courseid);
                $resendlink = html_writer::link($resendurl, $this->pix_icon('i/externallink', 'Повторная отправка'));
                $links .= ' ' . $resendlink;
            }

            if ($course->status == 200 && $isManager) {
                $editurl = helper::get_update_new_passport_url($course->courseid);
                $editlink = html_writer::link($editurl, $this->pix_icon('i/edit', get_string('edit')));
                $links .= ' ' . $editlink;
            }

            $editcell = new html_table_cell($links);

            $row = new html_table_row([
                $namecell,
                $urlcell,
                $giscell,
                $timecell,
                $statuscell,
                $editcell,
            ]);

            $data[] = $row;
            $index++;
        }
        $table->data = $data;
        return html_writer::table($table);
    }

    private function help_button(string $text): string
    {
        return <<<HTML
<a class="btn btn-link p-0" role="button"
    data-container="body" data-toggle="popover"
    data-placement="right" data-content="$text</pre>"
    data-html="true" tabindex="0" data-trigger="focus">
    <i class="icon fa fa-question-circle text-info fa-fw " role="img"></i>
</a>
HTML;

    }

    private function statusCell($course) : string
    {
        switch ($course->status ?? null) {
            case 200:
                return 'Успех';
            case null:
                return 'Неизвестная ошибка - ошибка отправки';
            default:
                return 'Ошибка ' . $course->status . $this->help_button(htmlspecialchars('<pre>'.json_encode(json_decode($course->response), JSON_PRETTY_PRINT).'</pre>'));
        }
    }

    /**
     * @return string HTML to output.
     */
    public function logs_table($data, $page, $perpage, $baseurl): string
    {
        $table = new html_table();
        $table->head  = [
            'URL',
            'Метод',
            'Статус',
            'Дата',
            'Действие',
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $rows = [];

        foreach ($data['data'] as $log) {
            // Url.
            $url = $log->url;
            $urlcell = new html_table_cell(s($url));

            $method = $log->method;
            $methodcell = new html_table_cell(s($method));

            $status = $log->status;
            $statuscell = new html_table_cell(s($status));

            $time = userdate($log->timecreated);
            $timecell = new html_table_cell(s($time));

            $links = '';
            // Action links.
            $viewurl = new \moodle_url('/local/onlineeduru/logs.php', [
                'id' => $log->id,
            ]);
            $viewlink = html_writer::link($viewurl, $this->pix_icon('t/hide', get_string('view')));
            $links .= ' ' . $viewlink;

            $row = new html_table_row([
                $urlcell,
                $methodcell,
                $statuscell,
                $timecell,
                $links,
            ]);

            $rows[] = $row;
        }
        $table->data = $rows;

        $table = html_writer::table($table);
        $paging_bar = $this->paging_bar($data['total'], $page, $perpage, $baseurl);

        return  $paging_bar . $table . $paging_bar;
    }

    public function render_log_page($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_onlineeduru/log_page', $data);
    }

    public function render_passport_view_page($page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_onlineeduru/passport_view_page', $data);
    }
}