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
        return $this->getProductById($productId);
    }

    public function getProductAttributeSet($productId = 0)
    {
        $productAttributeSet = 0;
        try {
            $productAttributeSet = (int) $this->helperEav->getProductEntityFieldValue($productId, "attribute_set_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productAttributeSet = 0;
        } finally {
            return $productAttributeSet;
        }
    }

    public function getProductBySku($productSku = null)
    {
        return $this->getProductById($this->getProductId($productSku));
    }

    public function getProductCreatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $productDate =  $this->helperEav->getProductEntityFieldValue($productId, "created_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getProductUpdatedDate($productId = 0)
    {
        $productDate = null;
        try {
            $productDate =  $this->helperEav->getProductEntityFieldValue($productId, "updated_at");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productDate = null;
        } finally {
            return $productDate;
        }
    }

    public function getProductDescription($productId)
    {
        $description = null;
        try {
            $description = $this->helperEav->getProductAttributeValue($productId, "description");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $description = null;
        } finally {
            return $description;
        }
    }

    public function getProductId($productSku = null)
    {
        $productId = 0;
        try {
            $sql = "SELECT entity_id FROM " . $this->tableProduct . " WHERE sku LIKE '$productSku'";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $productId = (int) $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productId = 0;
        } finally {
            return $productId;
        }
    }

    public function getProductImage($productId = 0, $width = 500)
    {
        $image = null;
        try {
            $imageFilename = $this->getProductImageFilename($productId);
            $image = $this->getProductImageUrl($productId, $imageFilename, $width);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    public function getProductImages($productId = 0, $width = 500)
    {
        $imageList = [];
        try {
            $images = $this->getProductImagesFilenames($productId);
            foreach ($images as $imageFilename) {
                array_push($imageList, $this->getProductImageUrl($productId, $imageFilename, $width));
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $imageList = [];
        } finally {
            return $imageList;
        }
    }

    public function getProductName($productId)
    {
        $name = null;
        try {
            $name = $this->helperEav->getProductAttributeValue($productId, "name");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $name = null;
        } finally {
            return $name;
        }
    }

    public function getProductSku($productId = 0)
    {
        $productSku = null;
        try {
            $productSku =  $this->helperEav->getProductEntityFieldValue($productId, "sku");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productSku = null;
        } finally {
            return $productSku;
        }
    }

    public function getProductStatus($productId)
    {
        $status = 0;
        try {
            $status = (int) $this->helperEav->getProductAttributeValue($productId, "status");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $status = 0;
        } finally {
            return $status;
        }
    }

    public function getProductStockQty($productId)
    {
        $qty = 0;
        try {
            $sql = "SELECT stock.qty as qty FROM " . $this->tableProductStock . " stock join " . $this->tableProduct . " product on stock.product_id = product.entity_id where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $qty = (int) $result;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $qty = -1;
        } finally {
            return $qty;
        }
    }

    public function getProductType($productId = 0)
    {
        $productType = null;
        try {
            $productType =  $this->helperEav->getProductEntityFieldValue($productId, "type_id");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productType = null;
        } finally {
            return $productType;
        }
    }

    public function getProductUrl($productId)
    {
        $productUrl = null;
        try {
            $urlKey = $this->getProductUrlKey($productId);
            $productUrl = $this->urlFramework->getUrl($urlKey);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $productUrl = null;
        } finally {
            return $productUrl;
        }
    }

    public function getProductUrlKey($productId)
    {
        $urlKey = null;
        try {
            $urlKey = $this->helperEav->getProductAttributeValue($productId, "url_key");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $urlKey = null;
        } finally {
            return $urlKey;
        }
    }

    public function productExists($productId)
    {
        $exists = false;
        try {
            $sql = "SELECT * FROM " . $this->tableProduct . " product where product.entity_id = $productId";
            $result = $this->helperDb->sqlQueryFetchOne($sql);
            $exists = $result && !empty($result);
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $exists = false;
        } finally {
            return $exists;
        }
    }

    private function getProductById($productId)
    {
        $product = null;
        try {
            $product = $this->productExists($productId) ? $this->productFactory->load($productId) : null;
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $product = null;
        } finally {
            return $product;
        }
    }

    private function getProductImageFilename($productId)
    {
        $image = null;
        try {
            $image = $this->helperEav->getProductAttributeValue($productId, "image");
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $image = null;
        } finally {
            return $image;
        }
    }

    private function getProductImagesFilenames($productId = 0)
    {
        $images = [];
        try {
            $sql = "SELECT gal.value AS fileName FROM " . $this->tableGalleryToEntity . " ent LEFT JOIN " . $this->tableGalleryValue . " val ON ent.entity_id= val.entity_id LEFT JOIN " . $this->tableGallery . " gal ON val.value_id = gal.value_id WHERE ent.entity_id = $productId GROUP BY gal.value";
            $result = $this->helperDb->sqlQueryFetchAll($sql);
            foreach ($result as $entry) {
                array_push($images, $entry["fileName"]);
            }
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $images = [];
        } finally {
            return $images;
        }
    }

    private function getProductImageUrl($productId = 0, $imageFilename = null, $width = 500)
    {
        $imageUrl = null;
        try {
            $product = $this->getProductById($productId);
            $imageUrl = $this->imageHelper->init($product, 'product_page_image_large')->keepAspectRatio(true)->setImageFile($imageFilename)->resize($width, null)->getUrl();
        } catch (\Exception $e) {
            $this->helperLog->errorLog(__METHOD__, $e->getMessage());
            $imageUrl = null;
        } finally {
            return $imageUrl;
        }
    }
}
