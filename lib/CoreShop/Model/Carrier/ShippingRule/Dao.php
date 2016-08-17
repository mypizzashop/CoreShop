<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Carrier\ShippingRule;

use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Model\Asset;

/**
 * Class Dao
 * @package CoreShop\Model\Carrier\ShippingRule
 */
class Dao extends AbstractDao
{
    /**
     * Mysql table name.
     *
     * @var string
     */
    protected static $tableName = 'coreshop_carrier_shippingrules';

    /**
     * @param array $data
     */
    protected function assignVariablesToModel($data)
    {
        parent::assignVariablesToModel($data);

        foreach ($data as $key => $value) {
            if ($key == 'actions') {
                $this->model->setActions(unserialize($value));
            } elseif ($key == 'conditions') {
                $this->model->setConditions(unserialize($value));
            }
        }
    }
}