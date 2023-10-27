<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_DeleteOrders
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\DeleteOrders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Helper Data to get system configuration values
 */
class Data extends AbstractHelper
{
    /**#@+
     * Constants defined to get the value of store Config.
     */
    public const DELETE_ORDERS_ENABLE = 'mi_delete_orders/general/enable_disable';
    public const DELETE_ORDERS_CREDIT_MEMO = 'mi_delete_orders/general/creditmemo';
    public const DELETE_ORDERS_INVOICE = 'mi_delete_orders/general/invoice';
    public const DELETE_ORDERS_SHIPMENT = 'mi_delete_orders/general/shipment';
    /**#@-*/

    /**
     * Module Enabled or Disabled
     *
     * @return string
     */
    public function isEnable(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Credit memo
     *
     * @return string
     */
    public function isDeleteOrderCreditmemo(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_CREDIT_MEMO,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Invoice
     *
     * @return string
     */
    public function isDeleteOrderInvoice(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_INVOICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Shipment
     *
     * @return string
     */
    public function isDeleteOrderShipment(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_SHIPMENT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
