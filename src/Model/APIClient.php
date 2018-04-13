<?php
/**
 * This file is part of project: TajawalAPI2.
 * Author: Ahmad Tawila <ahmad@tawila.net>
 * Date: 2018-04-09
 * Time: 12:01 AM
 */

namespace App\Model;

use GuzzleHttp\Client;

class APIClient implements APIClientInterface
{
    /**
     * @todo : move this value to some configuration/env variable
     */
    protected const REMOTE_API_URL = 'http://api.myjson.com/bins/tl0bp';

    /**
     * A flag to decide calling the real remote API or a debugging content from a local file.
     * @var bool
     */
    private $_testMode;

    /**
     * The file path to a file that should hold a the same results that should return from the real API
     * Meant for unit testing only
     *
     * @var string $_debuggingContent
     */
    private $_debuggingContent;

    /**
     * APIClient constructor.
     * @param bool $debug
     */
    public function __construct(bool $debug = false)
    {
        $this->_testMode = $debug;
    }

    /**
     * @param string $debuggingContent
     * @return APIClient
     */
    public function setDebuggingContentPath(string $debuggingContent): APIClient
    {
        $this->_debuggingContent = $debuggingContent;
        return $this;
    }

    /**
     * @param $filters
     * @param $sorting
     * @return array
     */
    public function getHotels($filters, $sorting): array
    {
        $data = $this->getDataFromRemoteService();

        $filteredData = array_filter($data->hotels, function ($hotel) use ($filters) {
            return $this->matchHotelAgainstFilters($hotel, $filters);
            //return true;
        });

        usort($filteredData, function ($a, $b) use ($sorting) {
            return $this->decideOrderBasedOnSortingCriteria($a, $b, $sorting);
        });

        return $filteredData;
    }

    /**
     * @return mixed
     */
    private function getDataFromRemoteService()
    {
        if ($this->_testMode && is_readable($this->_debuggingContent)) {
            return json_decode(file_get_contents($this->_debuggingContent));
        }

        $client = new Client();
        $response = $client->get(self::REMOTE_API_URL);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Initially assume the hotel matches the filers criteria, then walk through each existing filter to validate the hotel against.
     * Whenever a filter fails, stop the validations and return a miss-match.
     *
     * @param $hotel
     * @param array $filters
     * @return bool
     */
    private function matchHotelAgainstFilters($hotel, array $filters): bool
    {
        // the hotel is a match by default, until proved otherwise
        $match = true;

        foreach ($filters as $criteria => $value) {
            // break the loop if any previous filter missed.
            if (false === $match) {
                return $match;
            }

            switch ($criteria) {
                case 'hotel_name':
                    $match = ( !isset($value) || false !== stripos($hotel->name, $value));
                    break;
                case 'city':
                    $match = ( !isset($value) || strcasecmp($hotel->city, $value) === 0);
                    break;
                case 'price_max':
                    $match = ( !isset($value) || $hotel->price <= $value);
                    break;
                case 'price_min':
                    $match = ( !isset($value) || $hotel->price >= $value);
                    break;
                case 'start_date':
                    $startDate = $value;
                    break;
                case 'end_date':
                    $endDate = $value;
                    break;
            }
        }

        if ($match) {
            $dateIsInRange = false;
            if (isset($startDate, $endDate)) {
                foreach ($hotel->availability as $range) {
                    if ($dateIsInRange = ($this->isDateInRange($range->from, $range->to, $startDate)
                        && $this->isDateInRange($range->from, $range->to, $endDate))) {
                        break;
                    }
                }
            } elseif (isset($startDate)) {
                foreach ($hotel->availability as $range) {
                    if ($dateIsInRange = $this->isDateInRange($range->from, $range->to, $startDate)){
                        break;
                    }
                }
            } elseif (isset($endDate)) {
                foreach ($hotel->availability as $range) {
                    if ($dateIsInRange = $this->isDateInRange($range->from, $range->to, $endDate)){
                        break;
                    }
                }
            } else {
                // No date criteria in the filters at all, So Ignore date filtering & consider it in range
                $dateIsInRange = true;
            }
            $match = $match && $dateIsInRange;
        }

        return $match;
    }

    /**
     * @param $a
     * @param $b
     * @param $sorting
     * @return int
     * @todo Handle the case where multiple sorting criteria are passed
     */
    private function decideOrderBasedOnSortingCriteria($a, $b, $sorting): int
    {
        $keys = array_keys($sorting);
        if ('hotel_name' === reset($keys)) {
            $a = $a->name;
            $b = $b->name;
        } elseif ('price' === reset($keys)) {
            $a = $a->price;
            $b = $b->price;
        }

        $sort = ('desc' === strtolower(reset($sorting))) ? 'desc' : 'asc';

        $return = 0;
        if ('desc' === $sort) {
            $return = $b <=> $a;
        } elseif ('asc' === $sort) {
            $return = $a <=> $b;
        }
        return $return;
    }

    /**
     * Check if a Given date is within a date range or not.
     *
     * @param $startDate
     * @param $endDate
     * @param $givenDate
     * @return bool
     */
    private function isDateInRange($startDate, $endDate, $givenDate): bool
    {
        // Convert to timestamp
        $start_ts = strtotime($startDate);
        $end_ts = strtotime($endDate);
        $given_ts = strtotime($givenDate);

        // Check given date is between start & end
        return ($given_ts >= $start_ts) && ($given_ts <= $end_ts);
    }
}
