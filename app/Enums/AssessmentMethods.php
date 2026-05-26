<?php

namespace App\Enums;

enum AssessmentMethods: string
{
    case Exams = 'exams';
    case Projects = 'projects';
    case Attendance = 'attendance';
}
