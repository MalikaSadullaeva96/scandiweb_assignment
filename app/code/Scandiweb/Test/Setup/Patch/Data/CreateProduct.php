<?php
//setup:upgrade goes through the modules and checks whether there is any schema or data patches it needs to execute
namespace Scandiweb\Test\Setup\Patch\Data; //tells Magento that this .php os the part og Scandiweb_Test module;

//changes or updates to db during module installation or upgrade
use Magento\Framework\Setup\Patch\DataPatchInterface;
//allows to create tables, insert data, modify schema during module installation or upgrade-->
use Magento\Framework\Setup\ModuleDataSetupInterface;
//allows to work with product models:change quantity, color, etc
use Magento\Catalog\Model\ProductFactory;
//assign/remove products from category
use Magento\Catalog\Api\CategoryLinkManagementInterface;
//for area code
use Magento\Framework\App\State as AppState;


class CreateProduct implements DataPatchInterface
{
    private $moduleDataSetup;
    private $productFactory;
    private $storeManager;
    private $categoryLinkManagement;
    private $appState;


    //dependency injection
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductFactory $productFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        AppState $appState
    ){
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productFactory = $productFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->appState = $appState;
    }

    public function apply(){

        //check whether area code is set
        try {
            $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->appState->setAreaCode('adminhtml');
        }

        //start a setup process
        $this->moduleDataSetup->startSetup();
        //create the product
        $product = $this->productFactory->create();
        $product->setName('Scandiweb Test');
        $product->setSku('scandiweb_test');
        $product->setPrice(100);
        $product->setTypeId('simple');
        $product->setVisibility(4);
        $product->setStatus(1); //1-enabled
        $categoryId = ['2'];
        $this->categoryLinkManagement->assignProductToCategories($product->getSku(), $categoryId);

        $this->moduleDataSetup->endSetup();
    }

    public function  getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }

}
