<?php

declare(strict_types=1);

namespace Terravives\Fee\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Store\Model\Store;

class CreateCmsBlock implements DataPatchInterface, PatchRevertableInterface
{
    const CMS_BLOCK_IDENTIFIER = 'checkout_terravives_fee';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // check first if the block already exists
        $sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_BLOCK_IDENTIFIER, 'identifier');
        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->setContent('<div><img  src="https://terravives.bluehat.al/img/logo/cc_two.svg" alt="Logo" style="height:30px"><p>Our terms and conditions</p></div>')
                ->setIsActive(true)
                ->setStores([Store::DEFAULT_STORE_ID])
                ->setTitle('Terravives checkout block')
                ->save();

        }else{
            $this->blockFactory->create()
                ->setTitle('Terravives checkout block')
                ->setIdentifier(self::CMS_BLOCK_IDENTIFIER)
                ->setIsActive(true)
                ->setContent('<div><img  src="https://terravives.bluehat.al/img/logo/cc_two.svg" alt="Logo" style="height:30px"><p>Our terms and conditions</p></div>')
                ->setStores([Store::DEFAULT_STORE_ID])
                ->save();
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $sampleCmsBlock = $this->blockFactory
            ->create()
            ->load(self::CMS_BLOCK_IDENTIFIER, 'identifier');

        if ($sampleCmsBlock->getId()) {
            $sampleCmsBlock->delete();
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
