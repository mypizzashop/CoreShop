<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Plugin;
use CoreShop\Model\Wishlist;
use CoreShop\Controller\Action;
use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopProduct;

class CoreShop_WishlistController extends Action
{
    /**
     * @var Wishlist
     */
    protected $model;

    public function init()
    {
        parent::init();

        $this->disableLayout();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->prepareWishlist();
    }

    public function addAction()
    {
        $product_id = $this->getParam("product", null);
        $product = CoreShopProduct::getById($product_id);

        if ($product instanceof CoreShopProduct && $product->getEnabled() && $product->getAvailableForOrder()) {
            $checkAvailability = $this->model->allowedToAdd($product->getId());

            if ($checkAvailability === true) {
                $this->model->add($product->getId());

                $this->_helper->json(array("success" => true, "wishlist" => $this->model->getWishlist()));
            } else {
                if ($checkAvailability == 'limit_reached') {
                    $message = $this->view->translate('You reached the limit of products to your wishlist.');
                } elseif ($checkAvailability == 'already_added') {
                    $message = $this->view->translate('This product is already in your wishlist list.');
                } else {
                    $message = 'Error: ' . $checkAvailability;
                }

                $this->_helper->json(array("success" => false, "message" => $message));
            }
        }

        $this->_helper->json(array("success" => false, "wishlist" => $this->model->getWishlist()));
    }

    public function removeAction()
    {
        $product_id = $this->getParam("product", null);
        $product = CoreShopProduct::getById($product_id);

        if ($product instanceof CoreShopProduct) {
            $this->model->remove($product->getId());

            $this->_helper->json(array("success" => true, "wishlist" => $this->model->getWishlist()));
        }

        $this->_helper->json(array("success" => false, "wishlist" => $this->model->getWishlist()));
    }

    public function listAction()
    {
        $this->enableLayout();

        $this->view->headTitle($this->view->translate("Wishlist"));

        $productIds = $this->model->getWishlist();
        $products = array();

        if (!empty($productIds)) {
            $list = new Object\CoreShopProduct\Listing();
            $list->setCondition("oo_id IN (" . rtrim(str_repeat('?,', count($productIds)), ',').")", $productIds);

            $products = $list->getObjects();
        }

        $this->view->products = $products;
    }

    protected function prepareWishlist()
    {
        $this->model = new Wishlist();
    }
}