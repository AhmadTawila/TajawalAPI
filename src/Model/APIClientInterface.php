<?php
/**
 * This file is part of project: TajawalAPI2.
 * Author: Ahmad Tawila <ahmad@tawila.net>
 * Date: 2018-04-12
 * Time: 1:29 PM
 */

namespace App\Model;


interface APIClientInterface
{
    /**
     * Get a list of hotel inventory
     *
     * @param $filters
     * @param $sorting
     * @return array
     */
    public function getHotels($filters, $sorting): array;
}