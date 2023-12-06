<?php

namespace local_onlineeduru\model;

class passport
{
    /**
     * @var string Название онлайн-курса
     */
    public string $title;
    /**
     * @var string Дата ближайшего запуска YYYY-MM-DD
     */
    public string $started_at;
    /**
     * @var string|null Дата окончания онлайн-курса YYYY-MM-DD
     */
    public ?string $finished_at = null;
    /**
     * @var string|null Дата окончания записи на онлайн-курс
     */
    public ?string $enrollment_finished_at = null;
    /**
     * @var string Ссылка на изображение
     */
    public string $image;
    /**
     * @var string Описание онлайн-курса
     */
    public string $description;
    /**
     * @var string Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n
     */
    public string $competences;
    /**
     * @var string[] Массив строк – входных требований к обучающемуся
     */
    public array $requirements = [];
    /**
     * @var string Содержание онлайн-курса
     */
    public string $content;
    /**
     * @var string Ссылка на онлайн-курс на сайте Платформы
     */
    public string $external_url;
    /**
     * @var string[] Массив идентификаторов направлений в формате: “01.01.06”
     */
    public array $direction = [];
    /**
     * @var string Идентификатор Правообладателя
     */
    public string $institution;
    /**
     * @var course_duration Длительность онлайн-курса в неделях
     */
    public course_duration $duration;
    /**
     * @var int|null Количество лекций
     */
    public ?int $lectures = null;
    /**
     * @var string Язык онлайн-курса
     */
    public string $language;
    /**
     * @var bool Возможность получить сертификат
     */
    public bool $cert;
    /**
     * @var int|null Количество записей на сессию онлайн-курса
     */
    public ?int $visitors = null;
    /**
     * @var teacher[] Массив лекторов.
     */
    public array $teachers = [];

    /**
     * @var course_transfer[]|null Массив перезачётов.
     */
    public ?array $transfers = null;
    /**
     * @var string Результаты обучения
     */
    public string $results;
    /**
     * @var string Аккредитация
     */
    public string $accreditated;
    /**
     * @var int|null Объем онлайн-курса, в часах
     */
    public ?int $hours = null;
    /**
     * @var int|null Требуемое время для изучения онлайн-курса, часов в неделю
     */
    public ?int $hours_per_week = null;
    /**
     * @var string Версия курса
     */
    public string $business_version;
    /**
     * @var string|null Ссылка на проморолик
     */
    public ?string $promo_url = null;
    /**
     * @var string|null Язык проморолика
     */
    public ?string $promo_lang = null;
    /**
     * @var string|null Язык субтитров
     */
    public ?string $subtitles_lang = null;
    /**
     * @var string|null Оценочные средства
     */
    public ?string $estimation_tools = null;
    /**
     * @var string|null Используемый сервис прокторинга (либо перечень сервисов через “,”)
     */
    public ?string $proctoring_service = null;
    /**
     * @var string|null идентификатор сессии курса на платформе
     */
    public ?string $sessionid = null;
    /**
     * @var int Трудоёмкость курса в з.е.
     */
    public int $number;
    /**
     * @var string|null Тип(-ы) используемого(-ых) сервиса(-ов) прокторинга (либо перечень через “,”)
     */
    public ?string $proctoring_type;
    /**
     * @var string|null Текстовое описание системы оценивания (критерии и шкалы оценивания)
     */
    public ?string $assessment_description;

}