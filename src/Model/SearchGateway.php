<?php
/**
 * This file is part of project: TajawalAPI2.
 * Author: Ahmad Tawila <ahmad@tawila.net>
 * Date: 2018-04-10
 * Time: 9:25 PM
 */

namespace App\Model;

class SearchGateway
{
    /**
     * @var array The allowed filters for the search results.
     */
    private $_validFilters = [
        'hotel_name',
        'city',
        'price_max',
        'price_min',
        'start_date',
        'end_date'
    ];

    /**
     * @var array The allowed sorting criteria for the search results.
     */
    private $_validSorting = [
        'hotel_name',
        'price'
    ];

    /**
     * @var null|string
     */
    private $_defaultSearchProvider = APIClient::class;

    /**
     * @var APIClientInterface
     */
    private $_apiClient;

    /**
     * SearchGateway constructor.
     * @param null|string $searchProvider
     */
    public function __construct($searchProvider = null)
    {
        if ( null !== $searchProvider && class_exists($searchProvider) ) {
            $this->_defaultSearchProvider = $searchProvider;
        }
        $this->_apiClient = new $this->_defaultSearchProvider();
    }

    /**
     *
     *
     * @param array $filters
     * @param array $sorting
     * @return array
     * @throws \InvalidArgumentException
     */
    public function findHotelsBy(array $filters = [], array $sorting = []): array
    {
        $this->ValidateParameterNames($filters, $this->_validFilters, 'search filter');
        $this->ValidateParameterNames($sorting, $this->_validSorting, 'sorting criteria');

        // @todo The should be some serializer here to unify the result format. Ignored for now as no business need for it.
        return $this->_apiClient->getHotels($filters, $sorting);
    }

    /**
     * @param array $givenParams
     * @param array $validParams
     * @param $naming
     * @throws \InvalidArgumentException when finding a parameters name in $givenParams not existing in the $validParams array
     */
    private function ValidateParameterNames(array $givenParams, array $validParams, $naming): void
    {
        foreach ($givenParams as $k => $v ) {
            if (!\in_array($k, $validParams, true)) {
                throw new \InvalidArgumentException(
                    sprintf("Unknown $naming (%s), valid values for $naming are [%s]", $k, implode(', ', $validParams))
                );
            }
        }
    }

}