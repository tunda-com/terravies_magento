<?php
/**
 * Copyright © Terravies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Terravives\Fee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\SerializerInterface;

class ApiHelper extends AbstractHelper
{
	const API_BASE_URL            = 'terravives_fees/general/api_url';
	const API_BASE_KEY            = 'terravives_fees/general/api_key';

	const ENDPOINT_MAPPING_ELEMENTS = '/api/v1/GetProductMappingElements';
	const ENDPOINT_LOAD_OPTIONS = '/api/v1/LoadOptions';
	const ENDPOINT_COMPENSATION_ORDER = '/api/v1/CreateCompensationOrder';
	const ENDPOINT_CHECKOUT = '/api/v1/OrderCheckout';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Terravives\Fee\Logger\Logger
     */
    protected $_logger;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Terravives\Fee\Logger\Logger $logger,
        SerializerInterface $serializer,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->_logger = $logger;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function getProductMappingElements()
    {
        return $this->request('GET', self::ENDPOINT_MAPPING_ELEMENTS);
    }

    public function loadOptions($options)
    {
        return $this->request('POST', self::ENDPOINT_LOAD_OPTIONS, ['json' => $options]);
    }

    public function createCompensationOrder($options)
    {
        return $this->request('POST', self::ENDPOINT_COMPENSATION_ORDER, ['json' => $options]);
    }

    public function orderCheckout($options)
    {
        return $this->request('POST', self::ENDPOINT_CHECKOUT, ['json' => $options]);
    }

    /**
     * Get api base url
     *
     * @param null $storeId
     *
     * @return mixed
     */
    private function getBaseUrl($storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::API_BASE_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $value;
    }

    private function getApiKey($storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::API_BASE_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $value = $this->_encryptor->decrypt($value);
        return $value;
    }


    private function request(string $method, string $endpoint, array $options = [])
    {
        try {
            
            $url = $this->getBaseUrl() . $endpoint;
            $method = strtoupper($method);

            $curlOptions = $this->prepareCurlOptions($method, $url, $options);

            return $this->executeCurlRequest($curlOptions);
        } catch (\Exception $e) {
            // dd($this->_logger);
            $this->_logger->error( $endpoint . ' - ' . $e->getMessage());
            return false;
        }
    }

    private function prepareCurlOptions(string $method, string $url, array $options): array
    {
        $headers = [
            'Authorization: Bearer ' . $this->getApiKey(),
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];

        switch ($method) {
            case 'POST':
                $curlOptions[CURLOPT_POST] = true;
                break;
            case 'PUT':
            case 'DELETE':
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                break;
        }

        if (isset($options['json']) && $method !== 'DELETE') {
            $curlOptions[CURLOPT_POSTFIELDS] = $this->serializer->serialize($options['json']);
        }

        return $curlOptions;
    }

    private function executeCurlRequest(array $curlOptions): array
    {
        $curlHandle = curl_init();

        curl_setopt_array($curlHandle, $curlOptions);

        $response = curl_exec($curlHandle);
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        if (curl_errno($curlHandle)) {
            $errorMsg = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new \Exception("cURL error ({$httpCode}): {$errorMsg}");
        }

        if ($httpCode >= 400) {
            $errorMsg = $response;
            curl_close($curlHandle);
            throw new \Exception("HTTP error ({$httpCode}): {$errorMsg}");
        }

        curl_close($curlHandle);

        return $this->serializer->unserialize($response);
    }
}
