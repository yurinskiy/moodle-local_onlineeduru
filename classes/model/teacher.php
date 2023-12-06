<?php

namespace local_onlineeduru\model;

class teacher
{
    /**
     * @var string ФИО лектора
     */
    public string $display_name;

    /**
     * @var string Ссылка на изображение лектора
     */
    public string $image;

    /**
     * @var string|null Описание лектора
     */
    public ?string $description;
}