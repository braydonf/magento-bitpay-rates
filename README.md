BitPay Rates
------------

This is a Bitcoin Exchange Rate module for Magento that imports rates using the BitPay API ( https://bitpay.com/api/rates ). This plugin is to be used with the [BitPay Magento Plugin](https://github.com/bitpay/magento-plugin "Bitpay Magento Plugin Source Code").

Installation
------------

1. Install the plugin by copying the files into Magento.

2. Add BTC currency to your locale in /lib/Zend/Locale/Data/en.xml (or your locale)

``
<currency type="BTC">
    <displayName>Bitcoin</displayName>
    <displayName count="one">Bitcoin</displayName>
    <displayName count="other">Bitcoins</displayName>
    <symbol>฿</symbol>
</currency>
``

3. Add Bitcoin to the allowed curriencies in System -> Configuration -> Currency Setup and select Bitcoin as your Default Display Currency.

4. Import exchange rates using System -> Manage Currency -> Rates and also add a scheduled import using System -> Configuration -> Currency Setup -> Scheduled Import Settings
