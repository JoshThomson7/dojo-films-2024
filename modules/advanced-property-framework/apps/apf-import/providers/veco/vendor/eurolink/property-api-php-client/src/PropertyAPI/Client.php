<?php

namespace PropertyAPI;

use PropertyAPI\Property;

/**
 * PropertyAPI Client
 */
class Client extends \PropertyAPI\Base
{
    private $response;
    private $total = 0;
    private $count = 0;
    private $rows = [];
    private $parsedRows = [];
    private $parsedRow;

    public function getProperties($params = null)
    {
        $this->response = $this->request('', $params);

        $this->total = $this->response->Total;
        $this->count = $this->response->Count;
        $this->rows = ($this->response->Data ?: []);

        $this->parseRows();

        return $this;
    }

    public function getProperty($id)
    {
        $this->response = $this->request(sprintf('id/%d', $id));

        return ($this->response->Data ? $this->parseRow($this->response->Data[0]->_source) : new Property());
    }

    private function parseRows()
    {
        if ($this->total) {
            foreach ($this->rows as $key => $row) {
                $this->parsedRows[$key] = $this->parseRow($row->_source);
            }
        }
    }

    private function parseRow($row)
    {
        return new Property($row);
    }

    public function getRawResponse()
    {
        return $this->response;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getParsedRows()
    {
        return $this->parsedRows;
    }
}