## Tajawal Code Challenge solution
By Ahmad Tawila

The challenge details is mentioned [here](https://github.com/tajawal/code-challenge/blob/master/BE.md)

The Solution is implemented in PHP 7.2 with Symfony4 Framework, and PHPunit for testing.

------

Travis-CI 
[![Build Status](https://travis-ci.org/a-tawila/TajawalAPI.svg?branch=master)](https://travis-ci.org/a-tawila/TajawalAPI)

Scrutinizer-CI
[![Build Status](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/badges/build.png?b=master)](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/a-tawila/TajawalAPI/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


CodeClimate
[![Test Coverage](https://api.codeclimate.com/v1/badges/78a0911cbd102f786c30/test_coverage)](https://codeclimate.com/github/a-tawila/TajawalAPI/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/78a0911cbd102f786c30/maintainability)](https://codeclimate.com/github/a-tawila/TajawalAPI/maintainability)

------

### Assumptions
No assumptions for the moment

### Installation
You need a machine that has `PHP 7.2` and `Composer` installed.

- clone this repository
    ```
    git clone https://github.com/a-tawila/TajawalAPI.git TajawalAPI
    cd TajawalAPI
    ```
- install project dependencies using `Composer`
    ```
    composer install --dev
    ```
- run the local webserver
    ```
    php bin/console server:run
    ```
    
    The default URL for this installation from you local machine should be `http://localhost:8000`

### Usage

#### consuming the API
- using any RestAPI client like [Postman](https://www.getpostman.com/) call the following URL
    ```
    GET http://localhost:8000/search
    ```
- use the follwing parameters to narrow down your search results.
    ```
    f[hotel_name]=Gold  // a part of the hotel name, case insinsitive.
    f[city]=cairo       // city name must match the exact name, case insinsitive.
    f[price_max]=200    // int/float value (100, 90.5, 111.3, ... etc)
    f[price_min]=200
    f[start_date]=200   // start date, format dd-mm-yyyy (ex. 21-09-2020)
    f[end_date]=200     // end data same format
    
    s[hotel_name]=asc   // Sorting by hotel name (values 'asc' or 'desc', default 'asc')
    s[price]=asc        // Sorting by price      (values 'asc' or 'desc', default 'asc')
    ```
    
    example call URLs
    ```
    GET http://localhost:8000/search?f[city]=cairo&f[price_max]=200&s[price]=asc
    
    GET http://localhost:8000/search?f[hotel_name]=tuli
    
    GET http://localhost:8000/search
    ```
    
#### running the tests
Tests are written with PHPUnit 7
    

    cd TajawalAPI
    vendor/bin/simple-phpunit
