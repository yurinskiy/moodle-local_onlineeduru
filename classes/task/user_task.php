<?php

namespace local_onlineeduru\task;

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

        $sql = <<<SQL
select j.id, j.gis_courseid, j.gis_userid, j.sessionid, j.timecreated
  from {local_onlineeduru_user} j
 where j.gis_courseid is not null
   and j.gis_userid is not null
   and j.sessionid is not null
   and j.timerequest_created is null
 order by j.timecreated
SQL;

        $jobs = $DB->get_records_sql($sql);

        mtrace("Запланировано регистраций пользователей в ГИС СЦОС: " . \count($jobs));

        $i = 1;

        foreach ($jobs as $job) {
            mtrace("Отправка №{$i}", ' - ');

            $transaction = $DB->start_delegated_transaction();

            try {
                $data = [
                    'course_id' => $job->gis_courseid,
                    'user_id' => $job->gis_userid,
                    'session_id' => $job->sessionid,
                    'enroll_date' => date_format_string($job->timecreated, '%Y-%m-%dT%H:%M:%S%z'),
                ];

                $job->request_created = json_encode($data);
                //$job->timerequest_created = time();

                mtrace($job->request_created);


                $DB->update_record('local_onlineeduru_user', $job);

                $response = (new api())->createUser($job->request_created);

                $job->response_created = $response;

                $DB->update_record('local_onlineeduru_user', $job);

                $transaction->allow_commit();

                mtrace('Выполнено');
            } catch (\Exception $exception) {
                $transaction->rollback($exception);
                mtrace('Ошибка: ' . $exception->getMessage());
            }
        }

        mtrace("Завершение задания");
    }
}