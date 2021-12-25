<?php

namespace App\Domain\Planning;

interface IPlanningSynchronizeService
{
    public function reload() : array;
}