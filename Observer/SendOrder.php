<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace M2\Webhook\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class SendOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Curl $curl
     * @param LoggerInterface $logger
     */

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Curl $curl,
        LoggerInterface $logger
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_curl = $curl;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleStatus = $this->_scopeConfig->getValue('sales_order_webhook_integration_options/webhook_settings/active');
        $webhookUrl = $this->_scopeConfig->getValue('sales_order_webhook_integration_options/webhook_settings/sales_order_webhook_subdomain');
        $apiKey = $this->_scopeConfig->getValue('sales_order_webhook_integration_options/webhook_settings/connection_key');

        if ((int)$moduleStatus !== 1) {
            return;     // Module is inactive
        } else if (empty($webhookUrl) || empty($apiKey)) {
            return;     // Incorrect module configuration
        }

        $order = $observer->getEvent()->getOrder();  // Get order 
        $orderData = [
            "store_id" => $order->getStoreId(),
            "increment_id" => $order->getIncrementId(),
            "entity_id" => $order->getEntityId(),
            "id" => $order->getId(),
            "created_at" => $order->getCreatedAt()
        ];
        $this->_curl->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => trim($apiKey)
        ]);

        $this->_curl->setOptions([
            CURLOPT_TIMEOUT => 2 // Max timeout of CURL request in seconds
        ]);

        try {
            $this->_curl->post(sprintf($webhookUrl,  trim($apiKey)), json_encode($orderData));
        } catch (\Exception $e) {
            $this->_logger->debug(sprintf('Exception occurred with Webhook: %s', $e->getMessage()));
        }
    }
}
