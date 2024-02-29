<?php

    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;
    use PropertyAPI\Client;
    use PropertyAPI\Property;
    use GuzzleHttp\Exception\RequestException;

    final class PropertiesTest extends TestCase
    {
        private $client;
        private $properties;

        private function getAccessToken()
        {
            $dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/..' ));
            $dotenv->load();
            $dotenv->required('TOKEN')->notEmpty();

            return getenv('TOKEN');
        }

        private function setupClient()
        {
            $this->client = new Client([
                'accessToken' => $this->getAccessToken(),
            ]);
        }

        private function getProperties()
        {
           $this->properties = $this->client->getProperties();
        }

        public function testIsAccessTokenNotEmpty()
        {
            $this->assertNotEmpty($this->getAccessToken());
        }

        public function testCannotInstanciatePropertiesClassWithMissingToken()
        {
            $this->expectExceptionMessage('Access token is missing.');

            new Client([]);
        }

        public function testCannotInstanciatePropertiesClassWithEmptyToken()
        {
            $this->expectExceptionMessage('Access token is empty.');

            new Client([
                'accessToken' => '',
            ]);
        }

        public function testCannotInstanciatePropertiesClassWithInvalidToken()
        {
            $this->expectException(RequestException::class);

            $this->client = new Client([
                'accessToken' => '~abcd~',
            ]);

            $this->getProperties();

            // test total
            $this->assertEquals($this->properties->getTotal(), 0);
            $this->assertEquals(count($this->properties->getRows()), 0);
            $this->assertEquals(count($this->properties->getParsedRows()), 0);

            // test rows
            $this->assertInternalType('array', $this->properties->getRows());
            $this->assertInternalType('array', $this->properties->getParsedRows());
        }

        public function testCanInstanciatePropertiesClass()
        {
            $this->setupClient();

            $this->assertInstanceOf(Client::class, $this->client);
        }

        public function testCanCreateCollection()
        {
            $this->setupClient();

            $this->getProperties();

            $this->assertInstanceOf(Client::class, $this->properties);
        }

        public function testCanGetPropertiesCount()
        {
            $this->setupClient();

            $this->getProperties();

            // test total
            $this->assertInternalType('integer', $this->properties->getTotal());
            $this->assertEquals(count($this->properties->getRows()), 1);
            $this->assertEquals(count($this->properties->getParsedRows()), 1);

            // test rows
            $this->assertInternalType('array', $this->properties->getRows());
            $this->assertInternalType('array', $this->properties->getParsedRows());
        }

        public function testCanGetProperties()
        {
            $this->setupClient();

            $this->getProperties();

            $property = $this->properties->getParsedRows()[0];

            $this->assertInstanceOf(Property::class, $property);
            $this->assertInternalType('string', $property->getCompanyID());
            $this->assertEquals(strlen($property->getCompanyID()), 36);
            $this->assertInternalType('string', $property->getPropertyID());
            $this->assertEquals(strlen($property->getPropertyID()), 36);
            $this->assertInternalType('integer', $property->getID());
            $this->assertGreaterThan(1, $property->getID());
            $this->assertInternalType('array', $property->getBrochures());
            $this->assertInternalType('array', $property->getFeatures());
            $this->assertInternalType('object', $property->getAddress());
            $this->assertInternalType('array', $property->getAddressStringParts());
            $this->assertInternalType('string', $property->getAddressString());
            $this->assertInternalType('array', $property->getEPCImages());
            $this->assertInternalType('array', $property->getEPCDocuments());
            $this->assertInternalType('array', $property->getFloorPlans());
            $this->assertInternalType('boolean', $property->isFeatured());
            $this->assertInternalType('array', $property->getURLs());
            $this->assertInternalType('array', $property->getPhotos());
            $this->assertInternalType('object', $property->getLocation());
            $this->assertInternalType('array', $property->getVideoURLs());
        }
    }