<?php

namespace Hapex\Core\Helper;

use Magento\Framework\Url;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

class ProductHelper extends BaseHelper
{
    protected $tableProduct;
    protected $tableProductStock;
    protected $tableProductStockStatus;
    protected $tableProductCategory;
    protected $tableGallery;
    protected $tableGalleryValue;
    protected $tableGalleryToEntity;
    protected $helperEav;
    protected $productFactory;
    protected $imageHelper;
    protected $urlFramework;
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        ProductEavHelper $helperEav,
        ProductFactory $productFactory,
        ImageHelper $imageHelper,
        Url $urlFramework
    ) {
        parent::__construct($context, $objectManager);
        $this->helperEav = $helperEav;
        $this->productFactory = $productFactory->create();
        $this->imageHelper = $imageHelper;
        $this->urlFramework = $urlFramework;
        $this->tableProduct = $this->helperDb->getSqlTableName('catalog_product_entity');
        $this->tableProductStock = $this->helperDb->getSqlTableName('cataloginventory_stock_item');
        $this->tableProductStockStatus = $this->helperDb->getSqlTableName('cataloginventory_stock_status');
        $this->tableProductCategory = $this->helperDb->getSqlTableName('catalog_category_product');
        $this->tableGallery = $this->helperDb->getSqlTableName("catalog_product_entity_media_gallery");
        $this->tableGalleryValue = $this->helperDb->getSqlTableName("catalog_product_entity_media_gallery_value");
        $this->tableGalleryToEntity = $this->helperDb->getSqlTableName("catalog_product_entity_media_gallery_value_to_entity");
    }

    public function getEavHelper()
    {
        return $this->helperEav;
    }

    public function getProduct($productId)
    {
        return $this->getById($productId);
    }

    public function getAttributeSet($productId = 0)
    {
        $productAttributeSet = 0;
        try {
            $productAttributeSet = (int) $this->helperEav->getProductEntityFieldValue($productId, "attribute_set_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productAttributeSet = 0;
        } finally {
            return $productAttributeSet;
        }
    }

    public function getBySku($productSku = null)
    {
        return $this->getById($this->getId($productSku));
    }

    public function getSkusByStatus($status = 0)
    {
        $products = [];
        $tableStatus = $this->helperDb->getSqlTableName('catalog_product_entity_int');
        $tableStockStatus = $this->helperDb->getSqlTableName('cataloginventory_stock_status');
        $tableAttribute = $this->helperDb->getSqlTableName("eav_attribute");
        $sql = "SELECT entity_id FROM $tableStatus WHERE attribute_id = (SELECT attribute_id FROM $tableAttribute WHERE attribute_code LIKE 'status') AND $tableStatus.value = $status";
        $result = $this->helperDb->sqlQueryFetchAll($sql);
        if ($result) {
            foreach (array_column($result, "entity_id") as $entry) {
                $sku = $this->getSku($entry);
                if ($sku) $products[] = $sku;
            }
        }

        return $products;
    }

    public function getSkusListed()
    {
        $products = [];
        $tableStockStatus = $this->helperDb->getSqlTableName('cataloginventory_stock_status');
        $sql = "SELECT product_id FROM $tableStockStatus";
        $result = $this->helperDb->sqlQueryFetchAll($sql);
        if ($result) {
            foreach (array_column($result, "product_id") as $entry) {
                $sku = $this->getSku($entry);
                if ($sku) $products[] = $sku;
            }
        }

        return $products;
    }

    public function getProductCategories($productId = 0)
    {
        $categories = [];
        try {
            if ($productId > 0 && $this->productExists($productId)) {
                $sql = "SELECT category_id from " . $this->tableProductCategory . " WHERE product_id = $productId";
                $result = $this->helperDb->sqlQueryFetchAll($sql);
                if ($result) {
                    foreach (array_column($result, "category_id") as $entry) {
                        array_push($categories, $entry);
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $categories = [];
        } finally {
            return $categories;
        }
    }

    public function getCreatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $productDate =  $this->helperEav->getProductEntityFieldValue($productId, "created_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getUpdatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $productDate =  $this->helperEav->getProductEntityFieldValue($productId, "updated_at");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getDescription($productId = 0)
    {
        $description = null;
        try {
            $description = $this->helperEav->getProductAttributeValue($productId, "description");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getShortDescription($productId = 0)
    {
        $description = null;
        try {
            $description = $this->helperEav->getProductAttributeValue($productId, "short_description");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getId($productSku = null)
    {
        $productId = 0;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableProduct . " WHERE sku LIKE '$productSku'";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $productId = (int) $result;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productId = 0;
        } finally {
            return $productId;
        }
    }

    public function getImage($productId = 0, $width = 500)
    {
        $image = null;
        try {
            $imageFilename = $this->getImageFilename($productId);
            $image = $this->getImageUrl($productId, $imageFilename, $width);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    public function getImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            $images = $this->getImagesFilenames($productId);
            array_walk($images, function ($imageFilename) use (&$imageList, &$productId, &$width) {
                array_push($imageList, $this->getImageUrl($productId, $imageFilename, $width));
            });
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    public function getCost($productId = 0)
    {
        $cost = 0;
        try {
            $cost = (float) $this->helperEav->getProductAttributeValue($productId, "cost");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $cost = 0;
        } finally {
            return $cost;
        }
    }

    public function getPrice($productId = 0)
    {
        $price = 0;
        try {
            $price = (float) $this->helperEav->getProductAttributeValue($productId, "price");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $price = 0;
        } finally {
            return $price;
        }
    }

    public function getName($productId = 0)
    {
        $name = null;
        try {
            $name = $this->helperEav->getProductAttributeValue($productId, "name");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getSku($productId = 0)
    {
        $productSku = null;
        try {
            $productSku =  $this->helperEav->getProductEntityFieldValue($productId, "sku");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productSku = null;
        } finally {
            return $productSku;
        }
    }

    public function getStatus($productId = 0)
    {
        $status = 0;
        try {
            $status = (int) $this->helperEav->getProductAttributeValue($productId, "status");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getStockQty($productId = 0)
    {
        $qty = 0;
        try {
            $sql = "SELECT stock.qty as qty FROM " . $this->tableProductStock . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $qty = (int) $result;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = -1;
        } finally {
            return $qty;
        }
    }

    public function getStockListedQty($productId = 0)
    {
        $qty = 0;
        try {
            $sql = "SELECT stock.qty as qty FROM " . $this->tableProductStockStatus . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $qty = (int) $result;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = -1;
        } finally {
            return $qty;
        }
    }

    public function getStockStatus($productId = 0)
    {
        $in_stock = 0;
        try {
            $sql = "SELECT stock.is_in_stock as is_in_stock FROM " . $this->tableProductStock . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $in_stock = (int) $result;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $in_stock = 0;
        } finally {
            return $in_stock;
        }
    }


    public function getStockListedStatus($productId = 0)
    {
        $in_stock = 0;
        try {
            $sql = "SELECT stock.stock_status as stock_status FROM " . $this->tableProductStockStatus . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $in_stock = (int) $result;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $in_stock = 0;
        } finally {
            return $in_stock;
        }
    }

    public function getType($productId = 0)
    {
        $productType = null;
        try {
            $productType =  $this->helperEav->getProductEntityFieldValue($productId, "type_id");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productType = null;
        } finally {
            return $productType;
        }
    }

    public function getUrl($productId = 0)
    {
        $productUrl = null;
        try {
            $urlKey = $this->getUrlKey($productId);
            $productUrl = $this->urlFramework->getUrl($urlKey);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productUrl = null;
        } finally {
            return $productUrl;
        }
    }

    public function getUrlKey($productId = 0)
    {
        $urlKey = null;
        try {
            $urlKey = $this->helperEav->getProductAttributeValue($productId, "url_key");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $urlKey = null;
        } finally {
            return $urlKey;
        }
    }

    public function isProductAttributeSetName($productId = 0, $name = null)
    {
        return $this->getAttributeSet($productId) == $this->helperEav->getAttributeSetId($name, "catalog_product");
    }

    public function isProductAttributeSetNameLike($productId = 0, $name = null)
    {
        return $this->getAttributeSet($productId) == $this->helperEav->getAttributeSetIdLike($name, "catalog_product");
    }

    public function getAttributeSetName($productId = 0)
    {
        $setId = $this->getAttributeSet($productId);
        $setName = $this->helperEav->getAttributeSetName($setId, "catalog_product");
        return $setName;
    }

    public function productExists($productId = 0)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableProduct . " product where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    public function productSkuExists($sku = null)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableProduct . " product where product.sku = '$sku'";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    protected function getById($productId = 0)
    {
        $product = null;
        try {
            $product = $this->productExists($productId) ? $this->objectManager->create('Magento\Catalog\Model\Product')->load($productId) : null;
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $product = null;
        } finally {
            return $product;
        }
    }

    protected function getImageFilename($productId = 0)
    {
        $image = null;
        try {
            $image = $this->helperEav->getProductAttributeValue($productId, "image");
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    protected function getImagesFilenames($productId = 0)
    {
        $images = [];
        try {
            $sql = "SELECT gal.value AS fileName FROM " . $this->tableGalleryToEntity . " ent LEFT JOIN " . $this->tableGalleryValue . " val ON ent.entity_id= val.entity_id LEFT JOIN " . $this->tableGallery . " gal ON val.value_id = gal.value_id WHERE ent.entity_id = $productId AND gal.media_type = 'image' AND val.disabled = 0 GROUP BY gal.value ORDER BY val.position";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
            array_walk($result, function ($entry) use (&$images) {
                array_push($images, $entry["fileName"]);
            });
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $images = [];
        } finally {
            return $images;
        }
    }

    protected function getImageUrl($productId = 0, $imageFilename = null, $width = 500)
    {
        $imageUrl = null;
        try {
            $product = $this->getById($productId);
            $imageUrl = $this->imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($imageFilename)->resize($width, null)->getUrl();
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $imageUrl = null;
        } finally {
            return $imageUrl;
        }
    }
}
