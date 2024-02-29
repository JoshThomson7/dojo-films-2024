<?php

namespace PropertyAPI;

use Carbon\Carbon;

/**
 * Instance of a Property
 */
class Property extends \PropertyAPI\Base
{
    protected $mediaURI = 'https://passport.eurolink.co/api/properties/v1/media/';
    private $data;

    public function __construct($data = null)
    {
        $this->data = $data;

        return $this;
    }

    public function __get($name)
    {
        return (isset($this->data->{$name}) ? $this->data->{$name} : null);
    }

    private function parseDate($dateString)
    {
        return ($dateString ? new Carbon($dateString) : null);
    }

    public function isValid()
    {
        return isset($this->Property->PropertyID) && ! empty($this->Property->PropertyID);
    }

    public function getCompanyID()
    {
        return $this->CompanyID;
    }

    public function getPropertyID()
    {
        return $this->Property->PropertyID;
    }

    public function getID()
    {
        return (Integer) $this->WebID;
    }

    public function getWebStatus()
    {
        return $this->Property->Status;
    }

    public function statusOf($status)
    {
        return ($this->getWebStatus() === $status);
    }

    public function getBrochures()
    {
        $brochures = [];

        for ($i = 1; $i <= 2; $i++) {
            if ($this->Brochures->{'Document' . $i}) {
                $brochures[] = [
                    'url' => $this->mediaURI . $this->Brochures->{'Document' . $i},
                    'description' => $this->Brochures->{'Description' . $i},
                ];
            }
        }

        return $brochures;
    }

    public function getFeatures()
    {
        $features = [];

        for ($i = 1; $i <= 10; $i++) {
            if ($this->Features->{'Feature' . $i}) {
                $features[] = $this->Features->{'Feature' . $i};
            }
        }

        return $features;
    }

    public function getAddress()
    {
        return $this->Address;
    }

    public function getAddressStringParts()
    {
        $addressParts = [];

        foreach ($this->Address as $part) {
            if($part) {
                $addressParts[] = $part;
            }
        }

        return $addressParts;
    }

    public function getAddressString()
    {
        return implode(', ', $this->getAddressStringParts());
    }

    public function getShortAddress()
    {
        return trim($this->Property->ShortAddress);
    }

    public function getEPCImages()
    {
        $epcs = [];

        for ($i = 1; $i <= 2; $i++) {
            if ($this->EPCs->{'Image' . $i}) {
                $epcs[] = [
                    'url' => $this->mediaURI . $this->EPCs->{'Image' . $i},
                ];
            }
        }

        return $epcs;
    }

    public function getEPCDocuments()
    {
        $epcs = [];

        for ($i = 1; $i <= 2; $i++) {
            if ($this->EPCs->{'Document' . $i}) {
                $epcs[] = [
                    'url' => $this->mediaURI . $this->EPCs->{'Document' . $i},
                ];
            }
        }

        return $epcs;
    }

    public function getFeesDescription()
    {
        return $this->Fees->Description;
    }

    public function getFeesURL()
    {
        return $this->Fees->Link;
    }

    public function getTenure()
    {
        return $this->Tenure->Tenure;
    }

    public function getTenureType()
    {
        return $this->Tenure->TenureType;
    }

    public function getFloorPlans()
    {
        $floorPlans = [];

        for ($i = 1; $i <= 5; $i++) {
            if ($this->FloorPlans->{'Plan' . $i}) {
                $floorPlans[] = [
                    'url' => $this->mediaURI . $this->FloorPlans->{'Plan' . $i},
                ];
            }
        }

        return $floorPlans;
    }

    public function getCategory()
    {
        return $this->Property->Category;
    }

    public function isLettings()
    {
        return ($this->getCategory() === 'Lettings');
    }

    public function isSales()
    {
        return ($this->getCategory() === 'Sales');
    }

    public function getDescription()
    {
        return $this->Property->Description;
    }

    public function getCharge()
    {
        return $this->Property->Charge;
    }

    public function isFeatured()
    {
        return ($this->Property->Featured ? true : false);
    }

    public function isFeaturedDate()
    {
        return ($this->Property->FeaturedDate ? true : false);
    }

    private function parseFeaturedDate()
    {
        return $this->parseDate($this->Property->FeaturedDate);
    }

    public function getFeaturedDate()
    {
        return ($this->isFeatured() ? $this->parseFeaturedDate() : null);
    }

    public function getUpdatedDate()
    {
        return $this->parseDate($this->Property->UpdatedDate);
    }

    public function getCriteriaType()
    {
        return $this->Property->CriteriaType;
    }

    public function getAmount()
    {
        return $this->Property->Amount;
    }

    public function getAvailableFromDate()
    {
        return $this->parseDate($this->Property->AvailableFromDate);
    }

    public function getTown()
    {
        return $this->Property->Town;
    }

    public function getArea()
    {
        return $this->Property->Area;
    }

    public function getSummaryDescription()
    {
        return $this->Property->SummaryDescription;
    }

    public function getRentPeriod()
    {
        return $this->Property->RentPeriod;
    }

    public function getPriceQualifier()
    {
        return $this->Property->PriceStatus;
    }

    public function getPropertyType()
    {
        return $this->Property->PropertyType;
    }

    public function isCommerical()
    {
        return ($this->Property->PropertyType === 'Commercial Property');
    }

    public function getOutsideSpace()
    {
        return $this->Property->OutsideSpace;
    }

    public function getParking()
    {
        return $this->Property->Parking;
    }

    public function getFloors()
    {
        return $this->Property->Floors;
    }

    public function getBedrooms()
    {
        return $this->Property->Bedrooms;
    }

    public function getBathrooms()
    {
        return $this->Property->Bathrooms;
    }

    public function getFurnished()
    {
        return $this->Property->Furnished;
    }

    public function getSellingState()
    {
        return $this->Property->SellingState;
    }

    public function getMarketingDescription()
    {
        return $this->Property->MarketingDescription;
    }

    public function getMarketingDescriptionHTML()
    {
        return $this->Property->MarketingDescriptionHTML;
    }

    public function getNewProperty()
    {
        return $this->Property->NewProperty;
    }

    public function getKeywords()
    {
        return $this->Property->Keywords;
    }

    public function getGroundRent()
    {
        return $this->Property->GroundRent;
    }

    public function getNewHome()
    {
        return $this->Property->NewHome;
    }

    public function getInsertDate()
    {
        return $this->parseDate($this->Property->InsertDate);
    }

    public function getURLs()
    {
        $urls = [];

        for ($i = 1; $i <= 2; $i++) {
            if ($this->URLs->{'URL' . $i}) {
                $urls[] = $this->mediaURI . $this->URLs->{'URL' . $i};
            }
        }

        return $urls;
    }

    public function getOffice()
    {
        return $this->Office;
    }

    public function getOfficeID()
    {
        return $this->Office->ID;
    }

    public function getOfficeName()
    {
        return $this->Office->Name;
    }

    public function getOfficePhone()
    {
        return $this->Office->Phone;
    }

    public function getOfficeEmail()
    {
        return $this->Office->Email;
    }

    public function getOfficeManager()
    {
        return $this->Office->Manager;
    }

    public function getOfficeURL()
    {
        return $this->Office->Website;
    }

    public function getPhotos()
    {
        $photos = [];

        for ($i = 1; $i <= 25; $i++) {
            if ($this->Photos->{'Photo' . $i}) {
                $photos[] = [
                    'url' => $this->mediaURI . $this->Photos->{'Photo' . $i},
                    'description' => $this->Photos->{'Description' . $i},
                ];
            }
        }

        return $photos;
    }

    public function getPhotoUrl()
    {
        $photos = $this->getPhotos();

        return (isset($photos[0]) ? $photos[0]['url'] : null);
    }

    public function getPostcode()
    {
        return $this->Postcode->PostcodeFull;
    }

    public function getLocation()
    {
        return $this->Location;
    }

    public function getLongitude()
    {
        return $this->Location->Longitude;
    }

    public function getLatitude()
    {
        return $this->Location->Latitude;
    }

    public function getVideoURLs()
    {
        $videos = [];

        for ($i = 1; $i <= 4; $i++) {
            if ($this->Videos->{'Video' . $i}) {
                $videos[] = [
                    'url' => $this->mediaURI . $this->Videos->{'Video' . $i},
                    'description' => $this->Videos->{'Description' . $i},
                ];
            }
        }

        return $videos;
    }

    // Legacy methods from old Property API
    public function getTitle()
    {
        return $this->getShortAddress();
    }

    public function getStatus()
    {
        return $this->getWebStatus();
    }

    public function getPrice()
    {
        return $this->getAmount();
    }

    public function getClassification()
    {
        return $this->getPropertyType();
    }

    public function getAvailableDate()
    {
        return $this->getAvailableFromDate();
    }

}