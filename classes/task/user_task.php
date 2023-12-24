<?php

namespace local_onlineeduru\task;

use core\uuid;
use local_onlineeduru\services\api;

defined('MOODLE_INTERNAL') || die();

class user_task extends \core\task\scheduled_task
{
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name()
    {
        return 'Отправка информации в ГИС СЦОС';
    }

    /**
     * Execute the task.
     */
    public function execute()
    {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $this->sendResults();
        $this->sendDeleteParticipation();
        $this->sendCreateParticipation();

        mtrace("Завершение задания");
    }

    public function sendCreateParticipation()
    {
        global $CFG, $DB;

        mtrace("Отправка новых пользователей курса");

        $sql = <<<SQL
select j.id, j.gis_courseid, j.gis_userid, j.sessionid, j.timecreated
  from {local_onlineeduru_user} j
 where j.gis_courseid is not null
   and j.gis_userid is not null
   and j.sessionid is not null
   and j.uuid_created is null
 order by j.timecreated
SQL;

        $i = 1;

        while (($jobs = $DB->get_records_sql($sql, null, 0, 10)) && $i <= 1) {
            mtrace("Отправка пакета №{$i} записей в пакете " . \count($jobs));

            try {
                $data = [];

                $uuid = uuid::generate();

                foreach ($jobs as $job) {
                    $item = [
                        'course_id' => $job->gis_courseid,
                        'user_id' => $job->gis_userid,
                        'session_id' => $job->sessionid,
                        'enroll_date' => date_format_string($job->timecreated, '%Y-%m-%dT%H:%M:%S%z'),
                    ];

                    $job->uuid_created = $uuid;
                    $DB->update_record('local_onlineeduru_user', $job);

                    $data[] = $item;
                }

                mtrace(json_encode($data));

                $api = new api();
                $response = $api->createParticipation($uuid, json_encode($data));
                mtrace($response);

                if ($api->getStatus() != 200) {
                    throw new \LogicException($response);
                } else {
                    $responseJson = json_decode($response);

                    $j = 0;
                    foreach ($responseJson as $item) {
                        if (!empty($item->saved ?? null)) {
                            $j++;
                            continue;
                        }

                        $job = $DB->get_record('local_onlineeduru_user', [
                            'gis_courseid' => $item->course_id,
                            'gis_userid' => $item->user_id,
                            'sessionid' => $item->session_id,
                        ]);

                        $job->uuid_created = null;

                        $DB->update_record('local_onlineeduru_user', $job);
                    }

                    mtrace("Из " . \count($jobs) . " записей в пакете {$j} успешно принято в ГИС СЦОС");
                }
            } catch (\Exception $exception) {
                mtrace('Ошибка: ' . $exception->getMessage());
                foreach ($jobs as $job) {
                    $job->uuid_created = null;
                    $DB->update_record('local_onlineeduru_user', $job);
                }
            } finally {
                $i++;
            }
        }

        mtrace('Отправка новых пользователей курса - завершена');
    }


    public function sendDeleteParticipation()
    {
        global $CFG, $DB;

        mtrace("Отправка снятых с курса пользователей");

        $sql = <<<SQL
select j.id, j.gis_courseid, j.gis_userid, j.sessionid, j.timecreated
  from {local_onlineeduru_user} j
 where j.gis_courseid is not null
   and j.gis_userid is not null
   and j.sessionid is not null
   and j.timedeleted is not null
   and j.uuid_deleted is null
 order by j.timedeleted
SQL;

        $i = 1;

        while (($jobs = $DB->get_records_sql($sql, null, 0, 10)) && $i <= 1) {
            mtrace("Отправка пакета №{$i} записей в пакете " . \count($jobs));

            try {
                $data = [];

                $uuid = uuid::generate();

                foreach ($jobs as $job) {
                    $item = [
                        'course_id' => $job->gis_courseid,
                        'user_id' => $job->gis_userid,
                        'session_id' => $job->sessionid,
                    ];

                    $job->uuid_deleted = $uuid;
                    $DB->update_record('local_onlineeduru_user', $job);

                    $data[] = $item;
                }

                mtrace(json_encode($data));

                $api = new api();
                $response = $api->deleteParticipation($uuid, json_encode($data));
                mtrace($response);

                if ($api->getStatus() != 200) {
                    throw new \LogicException($response);
                } else {
                    $responseJson = json_decode($response);

                    $j = 0;
                    foreach ($responseJson as $item) {
                        if (!empty($item->saved ?? null)) {
                            $j++;
                            continue;
                        }

                        $job = $DB->get_record('local_onlineeduru_user', [
                            'gis_courseid' => $item->course_id,
                            'gis_userid' => $item->user_id,
                            'sessionid' => $item->session_id,
                        ]);

                        $job->uuid_deleted = null;

                        $DB->update_record('local_onlineeduru_user', $job);
                    }

                    mtrace("Из " . \count($jobs) . " записей в пакете {$j} успешно принято в ГИС СЦОС");
                }

            } catch (\Exception $exception) {
                mtrace('Ошибка: ' . $exception->getMessage());
                foreach ($jobs as $job) {
                    $job->uuid_deleted = null;
                    $DB->update_record('local_onlineeduru_user', $job);
                }
            } finally {
                $i++;
            }
        }

        mtrace('Отправка снятых с курса пользователей - завершена');
    }

    public function sendResults()
    {
        global $CFG, $DB;

        mtrace("Отправка оценок пользователей с курса");

        $sql = <<<SQL
select j.*
  from {local_onlineeduru_grade} j
 where j.gis_courseid is not null
   and j.gis_userid is not null
   and j.sessionid is not null
   and j.uuid_request is null
 order by j.timemodified
SQL;

        $i = 1;

        while (($jobs = $DB->get_records_sql($sql, null, 0, 10)) && $i <= 1) {
            mtrace("Отправка пакета №{$i} записей в пакете " . \count($jobs));

            try {
                $data = [];

                $uuid = uuid::generate();

                foreach ($jobs as $job) {
                    $item = [
                        'course_id' => $job->gis_courseid,
                        'session_id' => $job->sessionid,
                        'user_id' => $job->gis_userid,
                        'date' => date_format_string($job->timecreated, '%Y-%m-%dT%H:%M:%S%z'),
                        'rating' => $job->rating,
                        'checkpoint_name' => $job->checkpoint_name,
                        'checkpoint_id' => $job->checkpoint_id,
                    ];

                    $job->uuid_request = $uuid;
                    $DB->update_record('local_onlineeduru_grade', $job);

                    $data[] = $item;
                }

                mtrace(json_encode($data));

                $api = new api();
                $response = $api->sendCheckpoint($uuid, json_encode($data));
                mtrace($response);

                if ($api->getStatus() != 200) {
                    throw new \LogicException($response);
                } else {
                    $responseJson = json_decode($response);

                    $j = 0;
                    foreach ($responseJson as $item) {
                        if (!empty($item->saved ?? null)) {
                            $j++;
                            continue;
                        }

                        $job = $DB->get_record('local_onlineeduru_grade', [
                            'gis_userid' => $item->user_id,
                            'checkpoint_id' => $item->checkpoint_id,
                        ]);

                        $job->uuid_request = null;

                        $DB->update_record('local_onlineeduru_grade', $job);
                    }

                    mtrace("Из " . \count($jobs) . " записей в пакете {$j} успешно принято в ГИС СЦОС");
                }

            } catch (\Exception $exception) {
                mtrace('Ошибка: ' . $exception->getMessage());
                foreach ($jobs as $job) {
                    $job->uuid_request = null;
                    $DB->update_record('local_onlineeduru_grade', $job);
                }
            } finally {
                $i++;
            }
        }

        mtrace('Отправка снятых с курса пользователей - завершена');
    }
}