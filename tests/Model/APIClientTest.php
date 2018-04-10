<?php
/**
 * This file is part of project: TajawalAPI2.
 * Author: Ahmad Tawila <ahmad@tawila.net>
 * Date: 2018-04-09
 * Time: 10:24 PM
 */

namespace App\Tests\Model;

use App\Model\APIClient;
use PHPUnit\Framework\TestCase;

class APIClientTest extends TestCase
{
    /**
     * @var APIClient $_apiClient
     */
    private $_apiClient;

    public function setup()
    {
        $this->_apiClient = new APIClient(true);
        $this->_apiClient->setDebuggingContentPath(__DIR__ . '/api-mock.json');
    }

    /**
     * @param $searchTerm
     * @param $expected
     * @dataProvider getPartsOfHotelName
     */
    public function testSearchWithPartOfHotelNameMatchesTheHotel($searchTerm, $expected)
    {
        $data = $this->_apiClient->getHotels(['hotel_name' => $searchTerm], []);
        $this->assertEquals($expected, $data[0]->name );
    }

    public function getPartsOfHotelName()
    {
        // ['<the search term>' , '<the value that should match>']
        yield ['Gold', 'Golden Tulip'];
        yield ['old', 'Golden Tulip'];
        yield ['den tu', 'Golden Tulip'];
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $expectedCount
     * @dataProvider getDateRangesToSearchForAndExpectedResultsCount
     */
    public function testSearchWithDateRange($startDate, $endDate, $expectedCount)
    {
        $data = $this->_apiClient->getHotels(['start_date' => $startDate, 'end_date' => $endDate], []);
        $this->assertCount($expectedCount, $data);
    }

    public function getDateRangesToSearchForAndExpectedResultsCount()
    {
        yield ['10-10-2020', '15-10-2020', 4];  // available range
        yield ['10-10-2021', '15-10-2021', 0];  // un-available range
        yield ['01-12-2020', null, 1];          // available Start date
        yield ['01-12-2021', null, 0];          // un-available Start date
        yield [null, '01-12-2020', 1];          // available end date
        yield [null, '01-12-2021', 0];          // un-available end date
        yield [null, null, 6];                  // empty date range, should be ignored while filtering
    }

    /**
     * @param $city
     * @param $expectedCount
     * @dataProvider getCitiesToSearchForAndExpectedResultsCount
     */
    public function testSearchByCity($city, $expectedCount)
    {
        $data = $this->_apiClient->getHotels(['city' => $city], []);
        $this->assertCount($expectedCount, $data, "Expecting to find ($expectedCount) hotels in [$city], (".count($data). ') found');
    }

    public function getCitiesToSearchForAndExpectedResultsCount()
    {
        yield ['cairo', 1];
        yield ['Cairo', 1];
        yield ['CAIRO', 1];
        yield ['Cai', 0];       // you must give the exact full city name
        yield [null, 6];
    }

    /**
     * @param $maxPrice
     * @param $minPrice
     * @param $expectedCount
     * @dataProvider getPriceRangesToSearchForAndExpectedResultsCount
     */
    public function testSearchByPrice($maxPrice, $minPrice, $expectedCount)
    {
        $data = $this->_apiClient->getHotels(['price_max' => $maxPrice, 'price_min' => $minPrice], []);
        $this->assertCount($expectedCount, $data, "Expecting to find ($expectedCount) hotels in price range of [ $minPrice - $maxPrice ], (".count($data). ') found');
    }

    public function getPriceRangesToSearchForAndExpectedResultsCount()
    {
        yield ['100', '60', 3];
        yield ['190', '160', 0];
        yield ['70', '20', 0];
        yield ['100', null, 3];
        yield ['30', null, 0];
        yield [null, '90', 3];
        yield [null, '250', 0];
        yield [null, null, 6];
    }

    /**
     * @param $filters
     * @param $expectedCount
     * @dataProvider getMultipleFiltersToSearchForAndExpectedResultsCount
     */
    public function testSearchByMultipleFilters($filters, $expectedCount)
    {
        $data = $this->_apiClient->getHotels($filters, []);
        $this->assertCount($expectedCount, $data);
    }

    public function getMultipleFiltersToSearchForAndExpectedResultsCount()
    {
        yield [
            ['hotel_name' => 'hotel', 'price_max' => 80.6],
            2
        ];
        yield [
            ['hotel_name' => 'hotel', 'price_max' => 80.6, 'start_date' => '17-10-2020'],
            1
        ];
        yield [
            ['price_max' => 105, 'start_date' => '15-11-2020', 'city' => 'dubai'],
            1
        ];
    }

    /**
     * @param $sorting
     * @param $expectedOrder
     * @dataProvider getSortingCriteriaAndExpectedResultsCount
     */
    public function testSearchResultsAreSortedAccordingToSortingCriteria($sorting, $expectedOrder)
    {
        $data = $this->_apiClient->getHotels([], $sorting);
        $this->assertSame($expectedOrder[0], $data[0]->name);
        $this->assertSame($expectedOrder[1], $data[1]->name);
        $this->assertSame($expectedOrder[2], $data[2]->name);
    }

    public function getSortingCriteriaAndExpectedResultsCount()
    {
//        yield [
//            ['hotel_name' => 'asc', 'price' => 'asc'],
//            ['', '', '']
//        ];
//        yield [
//            ['hotel_name' => 'desc', 'price' => 'desc'],
//            ['', '', '']
//        ];
//        yield [
//            ['hotel_name' => 'desc', 'price' => 'asc'],
//            ['', '', '']
//        ];
//        yield [
//            ['hotel_name' => 'asc', 'price' => 'desc'],
//            ['', '', '']
//        ];
        yield [
            ['hotel_name' => 'asc'],
            ['Concorde Hotel', 'Golden Tulip', 'Le Meridien']
        ];
        yield [
            ['hotel_name' => 'desc'],
            ['Rotana Hotel', 'Novotel Hotel', 'Media One Hotel']
        ];
        yield [
            ['price' => 'asc'],
            ['Concorde Hotel', 'Rotana Hotel', 'Le Meridien']
        ];
        yield [
            ['price' => 'desc'],
            ['Novotel Hotel', 'Golden Tulip', 'Media One Hotel']
        ];
    }
}
