<?php

namespace App\Enums;

enum StatusCategory: string
{
    case Unit = 'unit';
    case Task = 'task';
    case Structural = 'structural';
    case Finishing = 'finishing';
    case Facade = 'facade';
}
