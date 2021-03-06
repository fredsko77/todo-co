<?php
namespace App\Traits;

use App\Entity\User;
use App\Repository\SourceRepository;

trait HelpersTrait
{

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];

        foreach (User::ROLES as $key => $value) {
            $roles[$key] = $key;
        }

        return $roles;
    }

}
