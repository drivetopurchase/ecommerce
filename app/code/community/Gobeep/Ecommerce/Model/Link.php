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
class Gobeep_Ecommerce_Model_Link extends Mage_Core_Helper_Abstract
{
    /**
     * Returns the Gobeep/Ecommerce game link
     * 
     * @param int $storeId Store ID
     * @return string
     */
    public function getGameLink($storeId)
    {
        return Mage::helper('gobeep_ecommerce')->getGameLink($storeId);
    }

    /**
     * Returns the Gobeep/Ecommerce cashier link
     * 
     * @param Mage_Sales_Model_Order $order Order object
     * @return string
     */
    public function getCashierLink($order)
    {
        $storeId = $order->getStoreId();
        $orderAmount = $order->getGrandTotal();
        $orderId = $order->getId();

        // Prepare the link
        return Mage::helper('gobeep_ecommerce')->getCashierLink([
            'order_amount' => Mage::getModel('directory/currency')->format(
                $orderAmount,
                ['display' => Zend_Currency::NO_SYMBOL],
                false
            ),
            'order_id' => $orderId,
            'referrer' => 'online',
        ], $storeId);
    }
}
