<?php

namespace App\Tests;

use App\Model\SearchGateway;
use PHPUnit\Framework\TestCase;

class SearchGatewayTest extends TestCase
{
    private $_searchGateway;

    public function setup()
    {
        $this->_searchGateway = new SearchGateway();
    }

    public function testSearchGatewayModelCanBeCalled(): void
    {
        $result = $this->_searchGateway->findHotelsBy();

        $this->assertInternalType(
            'array',
            $result,
            sprintf('Expecting an array, got something else (%s)', var_export($result, true))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSearchFiltersThrowsException(): void
    {
        $this->_searchGateway->findHotelsBy([
            'country' => 'EG'
        ], []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSortingCriteriaThrowsException(): void
    {
        $this->_searchGateway->findHotelsBy([], [
            'city' => 'Cairo'   // business requires you only sort by 'name' & 'price'
        ]);
    }
}
