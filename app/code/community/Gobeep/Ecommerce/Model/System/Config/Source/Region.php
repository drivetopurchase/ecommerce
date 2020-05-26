<?php

require_once 'vendor/autoload.php';

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

use Gobeep\Ecommerce\SdkInterface;

class Gobeep_Ecommerce_Model_System_Config_Source_Region
{
    public function toOptionArray()
    {
        return [
            ['value' => SdkInterface::REGION_EU, 'label' => Mage::helper('gobeep_ecommerce')->__('Europe')],
            ['value' => SdkInterface::REGION_AM, 'label' => Mage::helper('gobeep_ecommerce')->__('North-America')]
        ];
    }
}
