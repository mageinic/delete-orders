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

namespace MageINIC\DeleteOrders\Plugin\Order;

use MageINIC\DeleteOrders\Helper\Data as DataHelper;
use MageINIC\DeleteOrders\Plugin\PluginAbstract;
use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Sales\Block\Adminhtml\Order\View;

/**
 * class PluginAfter To add Delete Button on sales order view page
 */
class PluginAfter extends PluginAbstract
{
    /**
     * Delete Order Url for the Order View Form
     */
    public const DELETE_ORDER_FORM_URL = 'deleteorder/order/order';

    /**
     * @var Data
     */
    protected Data $data;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * PluginAfter constructor.
     *
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     * @param Data $data
     * @param DataHelper $dataHelper
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session      $authSession,
        Data         $data,
        DataHelper   $dataHelper
    ) {
        $this->data = $data;
        $this->dataHelper = $dataHelper;
        parent::__construct($aclRetriever, $authSession);
    }

    /**
     * After Plugin on getBackUrl Function
     *
     * @param View $subject
     * @param string $result
     * @return string
     */
    public function afterGetBackUrl(View $subject, string $result): string
    {
        if ($this->dataHelper->isEnable() && $this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = '<b>' . __('Note: ') . '</b>'
                . '<span style="color:#FF0000; display:table;">'
                . __('Invoices, Credit Memos and Shipments related to this order will also be deleted.')
                . '</span>' . '<br>'
                . __('Are you sure you want to do this?');

            if ($subject->getRequest()->getFullActionName() == 'sales_order_view') {
                $subject->addButton(
                    'mi-delete',
                    [
                        'label' => __('Delete Order'),
                        'onclick' => 'confirmSetLocation(\'' . $message . '\',\''
                            . $this->getDeleteUrl($params['order_id']) . '\')',
                        'class' => 'mi-delete'
                    ],
                    -1
                );
            }
        }
        return $result;
    }

    /**
     * Get Delete Order URL
     *
     * @param string $orderId
     * @return string
     */
    public function getDeleteUrl(string $orderId): string
    {
        return $this->data->getUrl(
            self::DELETE_ORDER_FORM_URL,
            ['order_id' => $orderId]
        );
    }
}
