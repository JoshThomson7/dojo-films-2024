<?php

    require realpath(__DIR__ . '/../vendor/autoload.php' );

    use PropertyAPI\Client;

    // these lines are only required for our example
    $dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/..' ));
    $dotenv->load();
    $dotenv->required('TOKEN')->notEmpty();

    try {

        $client = new Client([
            'accessToken' => getenv('TOKEN'),
        ]);

        // additional GET parameters can be passed in
        $properties = $client->getProperties([
            'size' => 1,
        ]);

        echo 'Total: ' . $properties->getTotal() . PHP_EOL;

        foreach($properties->getParsedRows() as $property) {

            echo PHP_EOL . 'getCompanyID()' . PHP_EOL;
            var_dump($property->getCompanyID());

            echo PHP_EOL . 'getPropertyID()' . PHP_EOL;
            var_dump($property->getPropertyID());

            echo PHP_EOL . 'getID()' . PHP_EOL;
            var_dump($property->getID());

            echo PHP_EOL . 'getWebStatus()' . PHP_EOL;
            var_dump($property->getWebStatus());

            echo PHP_EOL . 'getBrochures()' . PHP_EOL;
            var_dump($property->getBrochures());

            echo PHP_EOL . 'getFeatures()' . PHP_EOL;
            var_dump($property->getFeatures());

            echo PHP_EOL . 'getAddress()' . PHP_EOL;
            var_dump($property->getAddress());

            echo PHP_EOL . 'getAddressString()' . PHP_EOL;
            var_dump($property->getAddressString());

            echo PHP_EOL . 'getShortAddress()' . PHP_EOL;
            var_dump($property->getShortAddress());

            echo PHP_EOL . 'getTitle()' . PHP_EOL;
            var_dump($property->getTitle());

            echo PHP_EOL . 'getEPCImages()' . PHP_EOL;
            var_dump($property->getEPCImages());

            echo PHP_EOL . 'getEPCDocuments()' . PHP_EOL;
            var_dump($property->getEPCDocuments());

            echo PHP_EOL . 'getFeesDescription()' . PHP_EOL;
            var_dump($property->getFeesDescription());

            echo PHP_EOL . 'getFeesURL()' . PHP_EOL;
            var_dump($property->getFeesURL());

            echo PHP_EOL . 'getTenure()' . PHP_EOL;
            var_dump($property->getTenure());

            echo PHP_EOL . 'getTenureType()' . PHP_EOL;
            var_dump($property->getTenureType());

            echo PHP_EOL . 'getFloorPlans()' . PHP_EOL;
            var_dump($property->getFloorPlans());

            echo PHP_EOL . 'getCategory()' . PHP_EOL;
            var_dump($property->getCategory());

            echo PHP_EOL . 'isLettings()' . PHP_EOL;
            var_dump($property->isLettings());

            echo PHP_EOL . 'isSales()' . PHP_EOL;
            var_dump($property->isSales());

            echo PHP_EOL . 'getDescription()' . PHP_EOL;
            var_dump($property->getDescription());

            echo PHP_EOL . 'getCharge()' . PHP_EOL;
            var_dump($property->getCharge());

            echo PHP_EOL . 'isFeatured()' . PHP_EOL;
            var_dump($property->isFeatured());

            echo PHP_EOL . 'getFeaturedDate()' . PHP_EOL;
            var_dump($property->getFeaturedDate());

            echo PHP_EOL . 'getUpdatedDate()' . PHP_EOL;
            var_dump($property->getUpdatedDate());

            echo PHP_EOL . 'getCriteriaType()' . PHP_EOL;
            var_dump($property->getCriteriaType());

            echo PHP_EOL . 'getAmount()' . PHP_EOL;
            var_dump($property->getAmount());

            echo PHP_EOL . 'getPriceQualifier()' . PHP_EOL;
            var_dump($property->getPriceQualifier());

            echo PHP_EOL . 'getAvailableFromDate()' . PHP_EOL;
            var_dump($property->getAvailableFromDate());

            echo PHP_EOL . 'getTown()' . PHP_EOL;
            var_dump($property->getTown());

            echo PHP_EOL . 'getArea()' . PHP_EOL;
            var_dump($property->getArea());

            echo PHP_EOL . 'getSummaryDescription()' . PHP_EOL;
            var_dump($property->getSummaryDescription());

            echo PHP_EOL . 'getRentPeriod()' . PHP_EOL;
            var_dump($property->getRentPeriod());

            echo PHP_EOL . 'getPropertyType()' . PHP_EOL;
            var_dump($property->getPropertyType());

            echo PHP_EOL . 'getClassification()' . PHP_EOL;
            var_dump($property->getClassification());

            echo PHP_EOL . 'isCommerical()' . PHP_EOL;
            var_dump($property->isCommerical());

            echo PHP_EOL . 'getOutsideSpace()' . PHP_EOL;
            var_dump($property->getOutsideSpace());

            echo PHP_EOL . 'getParking()' . PHP_EOL;
            var_dump($property->getParking());

            echo PHP_EOL . 'getFloors()' . PHP_EOL;
            var_dump($property->getFloors());

            echo PHP_EOL . 'getBedrooms()' . PHP_EOL;
            var_dump($property->getBedrooms());

            echo PHP_EOL . 'getBathrooms()' . PHP_EOL;
            var_dump($property->getBathrooms());

            echo PHP_EOL . 'getFurnished()' . PHP_EOL;
            var_dump($property->getFurnished());

            echo PHP_EOL . 'getSellingState()' . PHP_EOL;
            var_dump($property->getSellingState());

            echo PHP_EOL . 'getMarketingDescription()' . PHP_EOL;
            var_dump($property->getMarketingDescription());

            echo PHP_EOL . 'getMarketingDescriptionHTML()' . PHP_EOL;
            var_dump($property->getMarketingDescriptionHTML());

            echo PHP_EOL . 'getNewProperty()' . PHP_EOL;
            var_dump($property->getNewProperty());

            echo PHP_EOL . 'getKeywords()' . PHP_EOL;
            var_dump($property->getKeywords());

            echo PHP_EOL . 'getGroundRent()' . PHP_EOL;
            var_dump($property->getGroundRent());

            echo PHP_EOL . 'getNewHome()' . PHP_EOL;
            var_dump($property->getNewHome());

            echo PHP_EOL . 'getInsertDate()' . PHP_EOL;
            var_dump($property->getInsertDate());

            echo PHP_EOL . 'getURLs()' . PHP_EOL;
            var_dump($property->getURLs());

            echo PHP_EOL . 'getOfficeID()' . PHP_EOL;
            var_dump($property->getOfficeID());

            echo PHP_EOL . 'getOfficeName()' . PHP_EOL;
            var_dump($property->getOfficeName());

            echo PHP_EOL . 'getOfficePhone()' . PHP_EOL;
            var_dump($property->getOfficePhone());

            echo PHP_EOL . 'getOfficeEmail()' . PHP_EOL;
            var_dump($property->getOfficeEmail());

            echo PHP_EOL . 'getOfficeManager()' . PHP_EOL;
            var_dump($property->getOfficeManager());

            echo PHP_EOL . 'getOfficeURL()' . PHP_EOL;
            var_dump($property->getOfficeURL());

            echo PHP_EOL . 'getPhotos()' . PHP_EOL;
            var_dump($property->getPhotos());

            echo PHP_EOL . 'getPostcode()' . PHP_EOL;
            var_dump($property->getPostcode());

            echo PHP_EOL . 'getLocation()' . PHP_EOL;
            var_dump($property->getLocation());

            echo PHP_EOL . 'getLongitude()' . PHP_EOL;
            var_dump($property->getLongitude());

            echo PHP_EOL . 'getLatitude()' . PHP_EOL;
            var_dump($property->getLatitude());

            echo PHP_EOL . 'getVideoURLs()' . PHP_EOL;
            var_dump($property->getVideoURLs());
        }

    } catch (Exception $event) {

        var_dump($event->getMessage());

    }

?>