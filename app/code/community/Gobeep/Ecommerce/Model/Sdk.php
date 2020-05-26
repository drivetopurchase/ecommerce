<?php

/**
 * GoBeep
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    GoBeep
 * @package     Gobeep_Ecommerce
 * @author      Christophe Eblé <ceble@gobeep.co>
 * @copyright   Copyright (c) GoBeep (https://gobeep.co)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'vendor/autoload.php';

use Gobeep\Ecommerce\Sdk;
use Gobeep\Ecommerce\SdkInterface;

class Gobeep_Ecommerce_Model_Sdk extends Mage_Core_Model_Abstract
{
    /**
     * Type constants
     */
    const TYPE_CASHIER = 'cashier';
    const TYPE_CAMPAIGN = 'campaign';

    /**
     * System config constants
     */
    const XML_PATH_ACTIVE = 'sales/gobeep_ecommerce/active';
    const XML_PATH_ENVIRONMENT = 'sales/gobeep_ecommerce/environment';
    const XML_PATH_REGION = 'sales/gobeep_ecommerce/region';
    const XML_PATH_CASHIER_ID = 'sales/gobeep_ecommerce/cashier_id';
    const XML_PATH_CAMPAIGN_ID = 'sales/gobeep_ecommerce/campaign_id';
    const XML_PATH_SECRET = 'sales/gobeep_ecommerce/secret';
    const XML_PATH_FROM_DATE = 'sales/gobeep_ecommerce/from_date';
    const XML_PATH_TO_DATE = 'sales/gobeep_ecommerce/to_date';
    const XML_PATH_ELIGIBLE_DAYS = 'sales/gobeep_ecommerce/eligible_days';
    const XML_PATH_CASHIER_IMAGE = 'sales/gobeep_ecommerce/cashier_image';
    const XML_PATH_EXT_CASHIER_IMAGE = 'sales/gobeep_ecommerce/cashier_external_image';
    const XML_PATH_CAMPAIGN_IMAGE = 'sales/gobeep_ecommerce/campaign_image';
    const XML_PATH_EXT_CAMPAIGN_IMAGE = 'sales/gobeep_ecommerce/campaign_external_image';
    const XML_PATH_NOTIFY = 'sales/gobeep_ecommerce/notify';
    const XML_PATH_REFUND_EMAIL_TEMPLATE = 'sales/gobeep_ecommerce/refund_email_template';
    const XML_PATH_WINNING_EMAIL_TEMPLATE = 'sales/gobeep_ecommerce/winning_email_template';
    const XML_PATH_TIMEZONE = 'general/locale/timezone';

    /**
     * Holds SDK instance
     * 
     * @var Gobeep\Ecommerce\Sdk
     */
    protected $sdk;

    /**
     * Initialize resource model
     */
    protected function _construct($store = null)
    {
        // Initialize SDK with system configuration values
        $this->sdk = new Sdk();
        $store = $store ?: Mage::app()->getStore()->getId();

        $this->addData([
            'is_active'               => Mage::getStoreConfig(self::XML_PATH_ACTIVE, $store),
            'environment'             => Mage::getStoreConfig(self::XML_PATH_ENVIRONMENT, $store) ?: SdkInterface::ENV_PRODUCTION,
            'region'                  => Mage::getStoreConfig(self::XML_PATH_REGION, $store) ?: SdkInterface::REGION_EU,
            'campaign_id'             => Mage::getStoreConfig(self::XML_PATH_CAMPAIGN_ID, $store),
            'cashier_id'              => Mage::getStoreConfig(self::XML_PATH_CASHIER_ID, $store),
            'secret'                  => Mage::getStoreConfig(self::XML_PATH_SECRET, $store),
            'from_date'               => Mage::getStoreConfig(self::XML_PATH_FROM_DATE, $store),
            'to_date'                 => Mage::getStoreConfig(self::XML_PATH_TO_DATE, $store),
            'eligible_days'           => Mage::getStoreConfig(self::XML_PATH_ELIGIBLE_DAYS, $store),
            'timezone'                => Mage::getStoreConfig(self::XML_PATH_TIMEZONE, $store) ?: 'Europe/Paris',
            'cashier_image'           => Mage::getStoreConfig(self::XML_PATH_CASHIER_IMAGE, $store),
            'external_cashier_image'  => Mage::getStoreConfig(self::XML_PATH_EXT_CASHIER_IMAGE, $store),
            'campaign_image'          => Mage::getStoreConfig(self::XML_PATH_CAMPAIGN_IMAGE, $store),
            'external_campaign_image' => Mage::getStoreConfig(self::XML_PATH_EXT_CAMPAIGN_IMAGE, $store)
        ]);

        // Initialize SDK
        $this->sdk->setEnvironment($this->getData('environment'))
            ->setRegion($this->getData('region'))
            ->setCampaignId($this->getData('campaign_id'))
            ->setCashierId($this->getData('cashier_id'))
            ->setSecret($this->getData('secret'))
            ->setTimezone($this->getData('timezone'));
    }

    /**
     * Check if we're ready to use the SDK
     * 
     * @param bool $advancedCheck Advanced check (also checks availability of graphical assets)
     * 
     * @return bool
     */
    public function isReady($advancedCheck = true)
    {
        // First check mandatory parameters
        if (!$this->getIsActive() || !$this->getCampaignId() || !$this->getCashierId() || !$this->getSecret()) {
            return false;
        }
        // Then check dates
        if (!$this->hasValidDates()) {
            return false;
        }
        // And check resources
        if ($advancedCheck) {
            return $this->hasValidResources();
        }

        return true;
    }

    /**
     * Checks if we have a cashier and campaign image
     * 
     * @return bool
     */
    public function hasValidResources()
    {
        return ($this->getCashierImage() || $this->getExternalCashierImage()) &&
            ($this->getCampaignImage() || $this->getExternalCampaignImage());
    }

    /**
     * Checks through SDK if dates are valid
     * 
     * @return bool
     */
    public function hasValidDates()
    {
        $date = date('Y-m-d H:i:s', strtotime('now'));

        // Check if date is in range
        $isDateInRange = $this->sdk->isDateInRange($date, $this->getFromDate(), $this->getToDate());
        $isDayEligible = !$this->getEligibleDays() || $this->sdk->isDayEligible($date, explode(',', $this->getEligibleDays()));

        return $isDateInRange && $isDayEligible;
    }

    /**
     * Returns the Gobeep/Ecommerce campaign link
     * 
     * @return string
     */
    public function getCampaignLink()
    {
        if (!$this->isReady()) {
            return '';
        }

        $campaignLink = '';
        try {
            $campaignLink = $this->sdk->getCampaignLink();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $campaignLink;
    }

    /**
     * Returns the Gobeep/Ecommerce cashier link
     * 
     * @param Mage_Sales_Model_Order $order Order object
     * 
     * @return string
     */
    public function getCashierLink(Mage_Sales_Model_Order $order)
    {
        if (!$this->isReady()) {
            return '';
        }

        $orderAmount = $order->getGrandTotal();
        $orderId = $order->getId();

        $cashierLink = '';
        try {
            $cashierLink = $this->sdk->getCashierLink([
                'order_amount' => Mage::getModel('directory/currency')->format(
                    $orderAmount,
                    ['display' => Zend_Currency::NO_SYMBOL],
                    false
                ),
                'order_id' => $orderId,
                'referrer' => 'online',
            ]);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $cashierLink;
    }

    /**
     * Signs payload
     * 
     * @param string $payload Payload
     * 
     * @return string
     */
    public function sign($payload)
    {
        $res = '';
        try {
            $res = $this->sdk->sign($payload);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $res;
    }

    /**
     * Returns either an external or internal cashier or campaign image
     * path based on system configuration
     * 
     * @param string $type Type
     * 
     * @return string
     */
    public function getImage($type = self::TYPE_CAMPAIGN)
    {
        $image = $this->getData("${type}_image");
        $externalImage = $this->getData("external_${type}_image");

        if (!empty($externalImage)) {
            return $externalImage;
        }

        return sprintf(
            '%stheme/%s',
            Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA),
            $image
        );
    }
}
