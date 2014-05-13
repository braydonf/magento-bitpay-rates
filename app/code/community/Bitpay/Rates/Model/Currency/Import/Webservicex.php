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
 * Overrides core Webservicex to add a public convert interface. When BitPay is enabled this is used as
 * backup for unsupported currencies.
 *
 * @category   Bitpay
 * @package    Bitpay_Rates
 * @author     Ticean Bennett <ticean@gmail.com>, Braydon Fuller <courier@braydon.com>
 */
class Bitpay_Rates_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Webservicex {
    /**
     * A public interface for converting currency.
     * @param  $currencyFrom
     * @param  $currencyTo
     * @param int $retry
     * @return void
     */
    public function convert($currencyFrom, $currencyTo, $retry=0) {
        $this->_convert($currencyFrom, $currencyTo, $retry);
    }
}
