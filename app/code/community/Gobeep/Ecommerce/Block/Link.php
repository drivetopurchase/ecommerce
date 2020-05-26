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

class Gobeep_Ecommerce_Block_Link extends Mage_Core_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->setTemplate('gobeep/link.phtml');
        parent::_construct();
    }

    /**
     * Checks if a link can be generated based on system configuration
     * parameters
     * 
     * @return bool
     */
    public function canLink()
    {
        if (!$this->hasData('for')) {
            return false;
        }

        $order = $this->getData('order');
        $store = $order ? $order->getStoreId() : null;

        $sdk = Mage::getSingleton('gobeep_ecommerce/sdk', [$store]);
        if (!$sdk->isReady()) {
            return false;
        }

        if ($this->getData('for') === Gobeep_Ecommerce_Model_Sdk::TYPE_CASHIER) {
            $orderAmount = $order->getGrandTotal();
            if ($orderAmount === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the image associated to the link
     * 
     * @return string
     */
    public function getImage()
    {
        $store = $this->getData('order') ? $this->getData('order')->getStoreId() : null;
        $sdk = Mage::getSingleton('gobeep_ecommerce/sdk', [$store]);

        if ($this->getData('for') === Gobeep_Ecommerce_Model_Sdk::TYPE_CASHIER) {
            return $sdk->getImage(Gobeep_Ecommerce_Model_Sdk::TYPE_CASHIER);
        }

        return $sdk->getImage(Gobeep_Ecommerce_Model_Sdk::TYPE_CAMPAIGN);
    }

    /**
     * Returns the link
     * 
     * @return string
     */
    public function getLink()
    {
        $store = $this->getData('order') ? $this->getData('order')->getStoreId() : null;
        $sdk = Mage::getSingleton('gobeep_ecommerce/sdk', [$store]);

        if ($this->getData('for') === Gobeep_Ecommerce_Model_Sdk::TYPE_CASHIER) {
            return $sdk->getCashierLink($this->getData('order'));
        }

        return $sdk->getCampaignLink();
    }
}
