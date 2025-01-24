<?php

namespace Enums;

enum TaskStatus: string
{
    case TODO = 'todo';

    case DONE = 'done';

    case IN_PROGRESS = 'in-progress';
}
