<?php

/*
 * @copyright Copyright © 2022 Bounteous. All rights reserved.
 * @author tanya.lamontagne
 */

namespace CokeEurope\Validations\Model;

use CokeEurope\Validations\Helper\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Psr\Log\LoggerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    private LoggerInterface $logger;
    private Config $config;

    public const ZIP_POSTAL_CODES_PATTERNS = [
        'zip_postal_code_patterns' => [
            'BE' => ['^\\d{4}$'],
            'FI' => ['^\\d{5}$'],
            'FR' => ['^\\d{5}$'],
            'DE' => [
                '^\\d{2}$',
                '^\\d{4}$',
                '^\\d{5}$'
            ],
            'IE' => [
                '^[0-9a-zA-Z]{3} [0-9a-zA-Z]{4}$',
                '^[0-9a-zA-Z]{7}$'
            ],
            'NL' => [
                '^\\d{4}\\s{0,1}[A-Za-z]{2}$',
                '^[0-9a-zA-Z]{6}$'
            ],
	        'GB' => [
				'^(([A-Z]{1,2}[0-9][A-Z0-9]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?CX|GIR ?0A{2}|SAN ?TA1)$',
		        '^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$'
	        ]
        ]
    ];

    public const PHONE_PATTERNS = [
        'phone_patterns' => [
            'BE' => ['^(((\+|00)32[ ]?(?:\(0\)[ ]?)?)|0){1}(4(60|[789]\d)\/?(\s?\d{2}\.?){2}(\s?\d{2})|(\d\/?\s?\d{3}|\d{2}\/?\s?\d{2})(\.?\s?\d{2}){2})$'],
            'FI' => ['^((04[0-9]{1})(\s?|-?)|050(\s?|-?)|0457(\s?|-?)|[+]?358(\s?|-?)50|0358(\s?|-?)50|00358(\s?|-?)50|[+]?358(\s?|-?)4[0-9]{1}|0358(\s?|-?)4[0-9]{1}|00358(\s?|-?)4[0-9]{1})(\s?|-?)(([0-9]{3,4})(\s|\-)?[0-9]{1,4})$'],
            'FR' => ['^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$'],
            'DE' => ['^((\+49)|(0049)|0)(\(?([\d \-\)\–\+\/\(]+){6,}\)?([ .\-–\/]?)([\d]+))$'],
            'IE' => ['^(0)?([1-9]\d{0,2})\d{7}$'],
            'NL' => ['^((\+31)|(0031)|0)(\(0\)|)(\d{1,3})(\s|\-|)(\d{8}|\d{4}\s\d{4}|\d{2}\s\d{2}\s\d{2}\s\d{2})$'],
	        'GB' => [
				'^((\(?0\d{4}\)?\s?\d{3}\s?\d{3})|(\(?0\d{3}\)?\s?\d{3}\s?\d{4})|(\(?0\d{2}\)?\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$',
		        '^(\+44\s?7\d{3}|\(?07\d{3}\)?)\s?\d{3}\s?\d{3}$',
		        '^(((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$',
		        '^\+?(?:\d\s?){10,12}$',
		        '^(((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$'
	        ]
        ]
    ];

    public const EUROPE_VALIDATIONS_BY_TYPE = [
        self::ZIP_POSTAL_CODES_PATTERNS,
        self::PHONE_PATTERNS
    ];

    /**
     * CheckoutConfigProvider constructor.
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return array|array[]
     */
    public function getConfig(): array
    {
        if (!$this->config->isCheckoutPostalValidationEnabled()) {
            return [];
        }

        $data = [];
        foreach (self::EUROPE_VALIDATIONS_BY_TYPE as $validationType) {
            foreach ($validationType as $key => $validation) {
                $data[$key] = $validation;
            }
        }

        try {
            return [
                'validations' => $data
            ];
        } catch (\Exception $e) {
            $this->logger->debug(
                __(
                    '[\CokeEurope\Validations\Model\CheckoutConfigProvider::getConfig] %1',
                    $e->getMessage()
                )
            );
            return [];
        }
    }
}
