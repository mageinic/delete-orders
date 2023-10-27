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

use MageINIC\DeleteOrders\Helper\Data as DataHelper;
use MageINIC\DeleteOrders\Model\Order\DeleteOrder;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * class MassDelete for delete order
 */
class MassDelete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'MageINIC_DeleteOrders::orders';

    /**
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * @var DeleteOrder
     */
    protected DeleteOrder $deleteOrder;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderRepository $orderRepository
     * @param DeleteOrder $deleteOrder
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context           $context,
        Filter            $filter,
        CollectionFactory $collectionFactory,
        OrderRepository   $orderRepository,
        DeleteOrder       $deleteOrder,
        DataHelper        $dataHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
        $this->orderRepository = $orderRepository;
        $this->deleteOrder = $deleteOrder;
        parent::__construct($context, $filter);
    }

    /**
     * @inheritdoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        if ($this->dataHelper->isEnable()) {
            $count = 0;

            /** @var OrderInterface $order */
            foreach ($collection->getItems() as $order) {
                $this->deleteOrder->deleteOrder($order);
                $count++;
            }
            if ($count) {
                $this->messageManager->addSuccessMessage(__('%1 order(s) have been deleted.', $count));
            } else {
                $this->messageManager->addErrorMessage(__('There is a problem.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Delete Order module is Disabled'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
