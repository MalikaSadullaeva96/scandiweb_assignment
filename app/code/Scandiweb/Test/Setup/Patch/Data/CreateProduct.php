<?php
declare(strict_types=1);
namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Framework\App\State as AppState;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;

class CreateProduct implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;
    /**
     * @var AppState
     */
    protected $appState;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var SourceItemInterfaceFactory
     */
    protected $sourceItemInterfaceFactory;
    /**
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSave;
    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ProductFactory $productFactory
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param AppState $appState
     * @param ProductRepositoryInterface $productRepository
     * @param SourceItemInterfaceFactory $sourceItemInterfaceFactory
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param EavSetup $eavSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductFactory $productFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        AppState $appState,
        ProductRepositoryInterface $productRepository,
        SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        SourceItemsSaveInterface $sourceItemsSave,
        EavSetup $eavSetup
    ){
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productFactory = $productFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->appState = $appState;
        $this->productRepository = $productRepository;
        $this->sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->eavSetup = $eavSetup;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply(): void
    {
        $this->appState->emulateAreaCode('adminhtml', [$this, 'execute']);
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function execute(): void
    {

        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, 'Default');
        $product = $this->productFactory->create();
        $product->setTypeId(Type::TYPE_SIMPLE)
            ->setAttributeSetId($attributeSetId)
            ->setName('Scandiweb Test')
            ->setSku('scandiweb_test')
            ->setPrice(100)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED);
        $product = $this->productRepository->save($product);

        $sourceItem = $this->sourceItemInterfaceFactory->create();
        $sourceItem->setSourceCode('default');
        $sourceItem->setSku($product->getSku());
        $sourceItem->setQuantity(100);
        $sourceItem->setStatus(1);
        $this->sourceItemsSave->execute([$sourceItem]);

    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
