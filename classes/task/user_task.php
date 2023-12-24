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
        return 'Регистрация пользователей на курс в ГИС СЦОС';
    }

    /**
     * Execute the task.
     */
    public function execute()
    {
        global $CFG, $DB;

        require_once($CFG->libdir . '/filelib.php');

        mtrace("Запуск задания");

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

        while (($jobs = $DB->get_records_sql($sql, null, 0, 10)) && $i <= 10) {
            mtrace("Отправка пакета №{$i} записей в пакете " . \count($jobs));

            $transaction = $DB->start_delegated_transaction();

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

                $response = (new api())->createParticipation($uuid, json_encode($data));

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

                $transaction->allow_commit();
            } catch (\Exception $exception) {
                $transaction->rollback($exception);
                mtrace('Ошибка: ' . $exception->getMessage());
            } finally {
                $i++;
            }
        }

        mtrace('Отправка новых пользователей курса - завершена');
    }
}