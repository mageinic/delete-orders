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

namespace MageINIC\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use MageINIC\DeleteOrders\Model\Order\DeleteOrder;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;

/**
 * class Order for delete order on sales order view page
 */
class Order extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'MageINIC_DeleteOrders::mageinic_delete_orders';

    /**
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var DeleteOrder
     */
    protected DeleteOrder $deleteOrder;

    /**
     * Order constructor.
     *
     * @param Action\Context $context
     * @param OrderRepository $orderRepository
     * @param DeleteOrder $deleteOrder
     */
    public function __construct(
        Action\Context  $context,
        OrderRepository $orderRepository,
        DeleteOrder     $deleteOrder
    ) {
        $this->deleteOrder = $deleteOrder;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $incrementId = $order->getIncrementId();
        try {
            $this->deleteOrder->deleteOrder($order);
            $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');

        return $resultRedirect;
    }
}
