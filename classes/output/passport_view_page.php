<?php

namespace local_onlineeduru\output;

use local_onlineeduru\model\passport;
use ReflectionClass;
use renderable;
use renderer_base;
use templatable;
use stdClass;

class passport_view_page implements renderable, templatable
{
    private $course;
    private $passport;

    public function __construct($course, $passport)
    {
        $this->course = $course;
        $this->passport = $this->loadJSON(new passport(),$passport->request);
    }

    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->course = $this->course;
        $data->passport = $this->passport;
        return $data;
    }

    function loadJSON($Obj, $json)
    {
        $dcod = json_decode($json);
        $prop = get_object_vars ( $dcod );
        $class = new ReflectionClass($Obj);
        foreach($prop as $key => $lock)
        {
            if($class->hasProperty($key))
            {
                if(is_object($dcod->$key))
                {
                    $cl = $class->getProperty($key)->getType()->getName();
                    $Obj->$key = $this->loadJSON(new $cl(), json_encode($dcod->$key));
                }
                else
                {
                    $Obj->$key = $dcod->$key;
                }
            }
        }
        return $Obj;
    }
}