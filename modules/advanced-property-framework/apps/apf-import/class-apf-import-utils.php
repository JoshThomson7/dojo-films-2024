<?php
/**
 * Utilities
 * 
 * @package APF
 * @version 2.0
 */

class APFI_Utils {

    /**
     * Converts PHP array to XML
     * 
     * @param array $format
     * @param object $xml_data
     * @param string $parent
     */
    public static function array_to_xml($data, $xml_data, $parent = '', $provider = '') {

		if(empty($data)) { return; }

        foreach($data as $key => $value) {
            if($parent === '') {
                $key = 'property';
            } elseif(is_numeric($key)) {
                $key = $parent.'_data';
            }

            if($provider === 'reapit-foundations') {
                if($key === 'summary') {
                    $value = str_replace("\r","\n", $value);
                    $value = explode(PHP_EOL, $value);
                }
            }

            if(is_array($value) || is_object($value)) {

                if($key === '_links' && $provider === 'reapit-foundations') { continue; }

				$subnode = $xml_data->addChild($key);
				self::array_to_xml($value, $subnode, $key, $provider);

            } elseif($value !== '' && !empty($value)) {
                $xml_data->addChild($key, htmlspecialchars($value));
            }

            /**
             * Provider specific
             */
            switch ($provider) {
                case 'reapit':
                    if($key === 'WeeklyRent') {
                        $xml_data->addChild("Department",htmlspecialchars('Lettings'));
                        $price = round(($value * 52) / 12, 2, PHP_ROUND_HALF_UP);
                        $xml_data->addChild("Price",htmlspecialchars($price));
            
                    } elseif($key === 'SalePrice') {
                        $xml_data->addChild("Department",htmlspecialchars('Sales'));
                        $xml_data->addChild("Price",htmlspecialchars($value));
                    }

                    break;
                
                default:
                    # do nothing
                    break;
            }
        
        }
    }

	public static function commasep_to_array($comma_sep) {

        return explode(',', $comma_sep);

    }

	public static function query_string($args) {

        if(empty($args)) { return; }

		$query_string = '';
		if(!empty($args)) {
			foreach ($args as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $item) {
						$query_string .= $key . '=' . urlencode($item) . '&';
					}
				} else {
					$query_string .= $key . '=' . urlencode($value) . '&';
				}
			}

			// Remove the trailing '&' character
			$query_string = rtrim($query_string, '&');
		}

		return $query_string;

    }

    public static function executionTime() {

        // Do stuff
        usleep(mt_rand(100, 10000));

        // At the end of your script
        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        return round($time, 2);

    }

}