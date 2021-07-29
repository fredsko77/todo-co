<?php

namespace App\Traits;

use DateTime;

trait ServicesTrait
{

    /**
     * now
     * @return string
     */
    public function now(): DateTime
    {
        return new DateTime('now');
    }

}
