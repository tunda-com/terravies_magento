<?php namespace Terravives\Fee\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    protected $cmsBlock;

    protected $apiHelper;

    protected $assetRepo;

    public function __construct(
        LayoutInterface $layout,
        \Terravives\Fee\Helper\ApiHelper $apiHelper,
        Repository $assetRepo
    )
    {
        $this->_layout = $layout;
        $this->apiHelper = $apiHelper;
        $this->assetRepo = $assetRepo;
    }

    public function getConfig(): array
    {
        $url = '#';
        if($urlFromApi = $this->apiHelper->getDisclaimer()){
            $url = $urlFromApi;
        }
        return [
            'terravives_block' => '<div class="terravives-terms-conditions"><img  src=' . $this->getImageUrl() .' alt="Logo" style="height:30px">
                                    <a href=' . $url .' target="_blank">' . __('Our Terms and conditions') . '</a>
                                  </div>'
        ];
    }

    public function getImageUrl()
    {
        return $this->assetRepo->getUrl('Terravives_Fee::images/logo-nero.png');
    }
}
