<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Preorder
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Preorder\Block\Product\View\Preorder;


class ProductConfigurable extends ProductAbstract
{
    /**
     * @return array
     */
    public function getConfigurableAttributes()
    {
        $attributes = [];
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $allowedAttributes = $typeInstance->getConfigurableAttributes($this->getProduct());
        foreach($allowedAttributes as $attribute) {
            $attributes[$attribute->getProductAttribute()->getId()] = 0;
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getProductPreorderMap()
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $elementaryProducts = $typeInstance->getUsedProducts($this->getProduct());
        $allowedAttributes = $typeInstance->getConfigurableAttributes($this->getProduct());

        $map = [];
        foreach ($elementaryProducts as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            if($this->helper->getIsProductPreorder($product)) {
                $map[$product->getId()] = [
                    'cartLabel' => $this->helper->getProductPreorderCartLabel($product),
                    'note'      => $this->helper->getProductPreorderNote($product),
                    'attributes' => []
                ];

                foreach($allowedAttributes as $attribute) {
                    $productAttribute = $attribute->getProductAttribute();
                    $productAttributeId = $productAttribute->getId();
                    $attributeValue = $product->getData($productAttribute->getAttributeCode());
                    $map[$product->getId()]['attributes'][$productAttributeId] = $attributeValue;
                }
            }
        }

        return $map;
    }

    /**
     * @return bool
     */
    public function getIsAllProductsPreorder()
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $elementaryProducts = $typeInstance->getUsedProducts($this->getProduct());
        $isAllProductsPreorder = true;
        foreach($elementaryProducts as $product) {
            if(!$this->helper->getIsProductPreorder($product)) {
                $isAllProductsPreorder = false;
            }
        }
        return $isAllProductsPreorder;
    }
}
