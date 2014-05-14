<?php
/**
 * Bitpay Rates
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Bitpay
 * @package     Bitpay_Rates
 * @copyright   Copyright (c) 2011 Ticean Bennett (http://www.scaleworks.co), 2014 Braydon Fuller (http://braydon.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency rate import model (From https://bitpay.com/api/rates)
 * Also, will default back to the default Webservicex for unsupported currencies.
 *
 * @category   Bitpay
 * @package    Bitpay_Rates
 * @author     Ticean Bennett <ticean@gmail.com>, Braydon Fuller <courier@braydon.com>
 */
class Bitpay_Rates_Model_Currency_Import_Bitpay extends Mage_Directory_Model_Currency_Import_Abstract {

    protected $_url = 'https://bitpay.com/api/rates';
    protected $_messages = array();

    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct() {
        $this->_httpClient = new Varien_Http_Client();
    }

    /**
     *
     * @return array An array of currencies supported on BitPay Bitcoin Exchange Rates.
     */
    public function getSupportedCurrencies() {
        return array("USD","EUR","GBP","JPY","CAD","AUD","CNY","CHF","SEK","NZD","KRW","AED","AFN","ALL","AMD","ANG","AOA","ARS","AWG","AZN","BAM","BBD","BDT","BGN","BHD","BIF","BMD","BND","BOB","BRL","BSD","BTN","BWP","BYR","BZD","CDF","CLF","CLP","COP","CRC","CVE","CZK","DJF","DKK","DOP","DZD","EEK","EGP","ETB","FJD","FKP","GEL","GHS","GIP","GMD","GNF","GTQ","GYD","HKD","HNL","HRK","HTG","HUF","IDR","ILS","INR","IQD","ISK","JEP","JMD","JOD","KES","KGS","KHR","KMF","KWD","KYD","KZT","LAK","LBP","LKR","LRD","LSL","LTL","LVL","LYD","MAD","MDL","MGA","MKD","MMK","MNT","MOP","MRO","MUR","MVR","MWK","MXN","MYR","MZN","NAD","NGN","NIO","NOK","NPR","OMR","PAB","PEN","PGK","PHP","PKR","PLN","PYG","QAR","RON","RSD","RUB","RWF","SAR","SBD","SCR","SDG","SGD","SHP","SLL","SOS","SRD","STD","SVC","SYP","SZL","THB","TJS","TMT","TND","TOP","TRY","TTD","TWD","TZS","UAH","UGX","UYU","UZS","VEF","VND","VUV","WST","XAF","XAG","XAU","XCD","XOF","XPF","YER","ZAR","ZMW","ZWL");
    }

    public function convert($currencyFrom, $currencyTo, $retry=0) {
        return $this->_convert($currencyFrom, $currencyTo, $retry);
    }

    protected function _convert($currencyFrom, $currencyTo, $retry=0) {
        $supported = $this->getSupportedCurrencies();

        if ( ! ( ( in_array( $currencyFrom, $supported ) && $currencyTo == 'BTC' ) ||
                 ( $currencyFrom == 'BTC' && in_array($currencyTo, $supported ) ) ) ) {

            $this->_messages[] = Mage::helper('directory')->__('Conversion from %s to %s is not supported by BitPay Bitcoin Exchange Rates will fallback to use Webservicex.', $currencyFrom, $currencyTo);

            try {
                $default = Mage::getModel('directory/currency_import_webservicex');
                return $default->convert($currencyFrom, $currencyTo);
            }
            catch (Exception $e) {
                $this->_messages[] = $e->getMessage();
            }

        }

        try {
            $response = $this->_httpClient
                ->setUri($this->_url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/bitpay/timeout')))
                ->request('GET')
                ->getBody();


            $prices = (array)json_decode($response);

            if( !$prices ) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $this->_url);
                return null;
            }

            $altCurrency = ($currencyFrom == "BTC") ? $currencyTo : $currencyFrom;

            foreach ( $prices as $price ) {
                if ( $price->code == $altCurrency ) {
                    $rate = $price->rate;
                }
            }

            if ( !$rate ) {
                return null;
            }
            if ( $currencyFrom == "BTC" ) {
                $result = (float)$rate;
            } else {
                $result = 1/(float)$rate;
            }

            $this->_messages[] = Mage::helper('directory')->__('Retrieved rate from %s to %s.', $currencyFrom, $currencyTo );

            return $result;

        } catch (Exception $e) {

            if ( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $_url);
            }

        }
    }
}
