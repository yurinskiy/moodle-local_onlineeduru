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

function xmldb_local_onlineeduru_upgrade($oldversion): bool
{
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023120900) {
        // Define field active to be added to local_onlineeduru_passport.
        $table = new xmldb_table('local_onlineeduru_passport');

        $field = new xmldb_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'type');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('local_onlineeduru_courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'active');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'local_onlineeduru_courseid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'usercreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'usermodified');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key('usercreated', XMLDB_KEY_FOREIGN, ['usercreated'], 'user', ['id']);
        $dbman->add_key($table, $key);
        $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $dbman->add_key($table, $key);
        $key = new xmldb_key('local_onlineeduru_courseid', XMLDB_KEY_FOREIGN, ['local_onlineeduru_courseid'], 'local_onlineeduru_course', ['id']);
        $dbman->add_key($table, $key);

        upgrade_plugin_savepoint(true, 2023120900, 'local', 'onlineeduru');
    }

    if ($oldversion < 2023121000) {

        // Define table local_onlineeduru_user to be created.
        $table = new xmldb_table('local_onlineeduru_user');

        // Adding fields to table local_onlineeduru_user.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gis_courseid', XMLDB_TYPE_CHAR, '36', null, null, null, null);
        $table->add_field('gis_userid', XMLDB_TYPE_CHAR, '36', null, null, null, null);
        $table->add_field('sessionid', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('request_created', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('response_created', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timedeleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('request_deleted', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('response_deleted', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timerequest_created', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timerequest_deleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_onlineeduru_user.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_onlineeduru_user.
        $table->add_index('local_onlineeduru_user_unique', XMLDB_INDEX_UNIQUE, ['courseid', 'userid', 'sessionid']);

        // Conditionally launch create table for local_onlineeduru_user.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Onlineeduru savepoint reached.
        upgrade_plugin_savepoint(true, 2023121000, 'local', 'onlineeduru');
    }

    if ($oldversion < 2023121100) {
        // Define table local_onlineeduru_curl to be created.
        $table = new xmldb_table('local_onlineeduru_curl');

        // Adding fields to table local_onlineeduru_curl.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('method', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('request', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('response', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_onlineeduru_curl.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for local_onlineeduru_curl.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field sessionid to be dropped from local_onlineeduru_user.
        $table = new xmldb_table('local_onlineeduru_user');

        foreach (['timerequest_deleted', 'timerequest_created', 'response_deleted', 'request_deleted', 'response_created', 'request_created'] as $name) {
            $field = new xmldb_field($name);

            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        // Define table local_onlineeduru_grade to be created.
        $table = new xmldb_table('local_onlineeduru_grade');

        // Adding fields to table local_onlineeduru_grade.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gis_courseid', XMLDB_TYPE_CHAR, '36', null, null, null, null);
        $table->add_field('gis_userid', XMLDB_TYPE_CHAR, '36', null, null, null, null);
        $table->add_field('sessionid', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null);
        $table->add_field('checkpoint_id', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null);
        $table->add_field('checkpoint_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('rating', XMLDB_TYPE_NUMBER, '5, 2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_onlineeduru_grade.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for local_onlineeduru_grade.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Onlineeduru savepoint reached.
        upgrade_plugin_savepoint(true, 2023121100, 'local', 'onlineeduru');
    }

    if ($oldversion < 2023121101) {

        // Define field uuid to be added to local_onlineeduru_curl.
        $table = new xmldb_table('local_onlineeduru_curl');
        $field = new xmldb_field('uuid', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null, 'timemodified');

        // Conditionally launch add field uuid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field uuid_created to be added to local_onlineeduru_user.
        $table = new xmldb_table('local_onlineeduru_user');
        $field = new xmldb_field('uuid_created', XMLDB_TYPE_CHAR, '36', null, null, null, null, 'timedeleted');

        // Conditionally launch add field uuid_created.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('uuid_deleted', XMLDB_TYPE_CHAR, '36', null, null, null, null, 'uuid_created');

        // Conditionally launch add field uuid_deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field uuid_request to be added to local_onlineeduru_grade.
        $table = new xmldb_table('local_onlineeduru_grade');
        $field = new xmldb_field('uuid_request', XMLDB_TYPE_CHAR, '36', null, null, null, null, 'timemodified');

        // Conditionally launch add field uuid_request.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Onlineeduru savepoint reached.
        upgrade_plugin_savepoint(true, 2023121101, 'local', 'onlineeduru');
    }



    // Everything has succeeded to here. Return true.
    return true;
}