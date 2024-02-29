<?php

    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;
    use PropertyAPI\Client;
    use PropertyAPI\Property;

    final class PropertyTest extends TestCase
    {
        private $client;
        private $property;

        private function getAccessToken()
        {
            $dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/..' ));
            $dotenv->load();
            $dotenv->required('TOKEN')->notEmpty();
            $dotenv->required('PROPERTY_ID')->notEmpty();

            return getenv('TOKEN');
        }

        private function setupClient()
        {
            $this->client = new Client([
                'accessToken' => $this->getAccessToken(),
            ]);
        }

        private function getProperty()
        {
           $this->property = $this->client->getProperty(getenv('PROPERTY_ID'));
        }

        public function testCanGetProperties()
        {
            $this->setupClient();

            $this->getProperty();

            $this->assertInstanceOf(Property::class, $this->property);
            $this->assertInternalType('string', $this->property->getCompanyID());
            $this->assertEquals(strlen($this->property->getCompanyID()), 36);
            $this->assertInternalType('string', $this->property->getPropertyID());
            $this->assertEquals(strlen($this->property->getPropertyID()), 36);
            $this->assertInternalType('integer', $this->property->getID());
            $this->assertGreaterThan(1, $this->property->getID());
            $this->assertInternalType('array', $this->property->getBrochures());
            $this->assertInternalType('array', $this->property->getFeatures());
            $this->assertInternalType('object', $this->property->getAddress());
            $this->assertInternalType('array', $this->property->getAddressStringParts());
            $this->assertInternalType('string', $this->property->getAddressString());
            $this->assertInternalType('array', $this->property->getEPCImages());
            $this->assertInternalType('array', $this->property->getEPCDocuments());
            $this->assertInternalType('array', $this->property->getFloorPlans());
            $this->assertInternalType('boolean', $this->property->isFeatured());
            $this->assertInternalType('array', $this->property->getURLs());
            $this->assertInternalType('array', $this->property->getPhotos());
            $this->assertInternalType('object', $this->property->getLocation());
            $this->assertInternalType('array', $this->property->getVideoURLs());
        }
    }