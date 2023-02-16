<?php
return [
    'scopes' => [
        'websites' => [
            'admin' => [
                'website_id' => '0',
                'code' => 'admin',
                'name' => 'Admin',
                'sort_order' => '0',
                'default_group_id' => '0',
                'is_default' => '0'
            ],
            'base' => [
                'website_id' => '1',
                'code' => 'base',
                'name' => 'Main Website',
                'sort_order' => '0',
                'default_group_id' => '1',
                'is_default' => '0'
            ],
            'egypt_website' => [
                'website_id' => '2',
                'code' => 'egypt_website',
                'name' => 'Coke Egypt',
                'sort_order' => '1',
                'default_group_id' => '2',
                'is_default' => '1'
            ],
            'topo_chico_gr_website' => [
                'website_id' => '3',
                'code' => 'topo_chico_gr_website',
                'name' => 'Topo Chico GR',
                'sort_order' => '0',
                'default_group_id' => '3',
                'is_default' => '0'
            ],
            'coke_uk' => [
                'website_id' => '9',
                'code' => 'coke_uk',
                'name' => 'Coke United Kingdom',
                'sort_order' => '0',
                'default_group_id' => '9',
                'is_default' => '0'
            ],
            'coke_eu' => [
                'website_id' => '12',
                'code' => 'coke_eu',
                'name' => 'Coke Europe',
                'sort_order' => '0',
                'default_group_id' => '21',
                'is_default' => '0'
            ],
            'olnb_norway' => [
                'website_id' => '15',
                'code' => 'olnb_norway',
                'name' => 'Open Like Never Before Norway',
                'sort_order' => '0',
                'default_group_id' => '30',
                'is_default' => '0'
            ],
            'olnb_turkey' => [
                'website_id' => '18',
                'code' => 'olnb_turkey',
                'name' => 'Open Like Never Before Turkey',
                'sort_order' => '0',
                'default_group_id' => '33',
                'is_default' => '0'
            ],
            'france_d2c' => [
                'website_id' => '23',
                'code' => 'france_d2c',
                'name' => 'Coke France',
                'sort_order' => '0',
                'default_group_id' => '35',
                'is_default' => '0'
            ],
            'jp_marche' => [
                'website_id' => '26',
                'code' => 'jp_marche',
                'name' => 'Coke Japan Marche',
                'sort_order' => '0',
                'default_group_id' => '38',
                'is_default' => '0'
            ]
        ],
        'groups' => [
            0 => [
                'group_id' => '0',
                'website_id' => '0',
                'name' => 'Default',
                'root_category_id' => '0',
                'default_store_id' => '0',
                'code' => 'default'
            ],
            1 => [
                'group_id' => '1',
                'website_id' => '1',
                'name' => 'Main Website Store',
                'root_category_id' => '2',
                'default_store_id' => '1',
                'code' => 'main_website_store'
            ],
            2 => [
                'group_id' => '2',
                'website_id' => '2',
                'name' => 'Coke Egypt',
                'root_category_id' => '2',
                'default_store_id' => '2',
                'code' => 'egypt_store'
            ],
            3 => [
                'group_id' => '3',
                'website_id' => '3',
                'name' => 'Topo Chico GR',
                'root_category_id' => '36',
                'default_store_id' => '9',
                'code' => 'topo_chico_gr'
            ],
            9 => [
                'group_id' => '9',
                'website_id' => '9',
                'name' => 'Great Britain',
                'root_category_id' => '45',
                'default_store_id' => '18',
                'code' => 'great_britain'
            ],
            12 => [
                'group_id' => '12',
                'website_id' => '9',
                'name' => 'Northern Ireland',
                'root_category_id' => '45',
                'default_store_id' => '21',
                'code' => 'northern_ireland'
            ],
            15 => [
                'group_id' => '15',
                'website_id' => '12',
                'name' => 'Belgium',
                'root_category_id' => '45',
                'default_store_id' => '24',
                'code' => 'belgium'
            ],
            18 => [
                'group_id' => '18',
                'website_id' => '12',
                'name' => 'Ireland',
                'root_category_id' => '45',
                'default_store_id' => '30',
                'code' => 'ireland'
            ],
            21 => [
                'group_id' => '21',
                'website_id' => '12',
                'name' => 'Germany',
                'root_category_id' => '45',
                'default_store_id' => '33',
                'code' => 'germany'
            ],
            24 => [
                'group_id' => '24',
                'website_id' => '12',
                'name' => 'Netherlands',
                'root_category_id' => '45',
                'default_store_id' => '36',
                'code' => 'netherlands'
            ],
            27 => [
                'group_id' => '27',
                'website_id' => '12',
                'name' => 'Finland',
                'root_category_id' => '45',
                'default_store_id' => '39',
                'code' => 'finland'
            ],
            30 => [
                'group_id' => '30',
                'website_id' => '15',
                'name' => 'Norway',
                'root_category_id' => '45',
                'default_store_id' => '42',
                'code' => 'olnb_norway'
            ],
            33 => [
                'group_id' => '33',
                'website_id' => '18',
                'name' => 'Turkey',
                'root_category_id' => '45',
                'default_store_id' => '45',
                'code' => 'olnb_turkey'
            ],
            35 => [
                'group_id' => '35',
                'website_id' => '23',
                'name' => 'Coke France',
                'root_category_id' => '2',
                'default_store_id' => '47',
                'code' => 'france_d2c'
            ],
            38 => [
                'group_id' => '38',
                'website_id' => '26',
                'name' => 'Coke Japan Marche Store',
                'root_category_id' => '113',
                'default_store_id' => '50',
                'code' => 'jp_marche_store'
            ],
            39 => [
                'group_id' => '39',
                'website_id' => '12',
                'name' => 'France',
                'root_category_id' => '45',
                'default_store_id' => '47',
                'code' => 'france'
            ]
        ],
        'stores' => [
            'admin' => [
                'store_id' => '0',
                'code' => 'admin',
                'website_id' => '0',
                'group_id' => '0',
                'name' => 'Admin',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'default' => [
                'store_id' => '1',
                'code' => 'default',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'Default Store View',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'egypt' => [
                'store_id' => '2',
                'code' => 'egypt',
                'website_id' => '2',
                'group_id' => '2',
                'name' => 'عربي',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'egypt_en' => [
                'store_id' => '3',
                'code' => 'egypt_en',
                'website_id' => '2',
                'group_id' => '2',
                'name' => 'ENGLISH',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'topo_chico_gr_en' => [
                'store_id' => '6',
                'code' => 'topo_chico_gr_en',
                'website_id' => '3',
                'group_id' => '3',
                'name' => 'English',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'topo_chico_gr_gr' => [
                'store_id' => '9',
                'code' => 'topo_chico_gr_gr',
                'website_id' => '3',
                'group_id' => '3',
                'name' => 'Greek',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'great_britain_english' => [
                'store_id' => '18',
                'code' => 'great_britain_english',
                'website_id' => '9',
                'group_id' => '9',
                'name' => 'English',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'northern_ireland_english' => [
                'store_id' => '21',
                'code' => 'northern_ireland_english',
                'website_id' => '9',
                'group_id' => '12',
                'name' => 'English',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'belgium_french' => [
                'store_id' => '24',
                'code' => 'belgium_french',
                'website_id' => '12',
                'group_id' => '15',
                'name' => 'French',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'belgium_dutch' => [
                'store_id' => '27',
                'code' => 'belgium_dutch',
                'website_id' => '12',
                'group_id' => '15',
                'name' => 'Dutch',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'ireland_english' => [
                'store_id' => '30',
                'code' => 'ireland_english',
                'website_id' => '12',
                'group_id' => '18',
                'name' => 'English',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'germany_german' => [
                'store_id' => '33',
                'code' => 'germany_german',
                'website_id' => '12',
                'group_id' => '21',
                'name' => 'German',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'netherlands_dutch' => [
                'store_id' => '36',
                'code' => 'netherlands_dutch',
                'website_id' => '12',
                'group_id' => '24',
                'name' => 'Dutch',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'finland_finnish' => [
                'store_id' => '39',
                'code' => 'finland_finnish',
                'website_id' => '12',
                'group_id' => '27',
                'name' => 'Finnish',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'france_french' => [
                'store_id' => '52',
                'code' => 'france_french',
                'website_id' => '12',
                'group_id' => '39',
                'name' => 'France',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'norway_norwegian' => [
                'store_id' => '42',
                'code' => 'norway_norwegian',
                'website_id' => '15',
                'group_id' => '30',
                'name' => 'Norwegian',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'turkey_turkish' => [
                'store_id' => '45',
                'code' => 'turkey_turkish',
                'website_id' => '18',
                'group_id' => '33',
                'name' => 'Turkish',
                'sort_order' => '1',
                'is_active' => '1'
            ],
            'france_d2c' => [
                'store_id' => '47',
                'code' => 'france_d2c',
                'website_id' => '23',
                'group_id' => '35',
                'name' => 'French',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'jp_marche_ja' => [
                'store_id' => '50',
                'code' => 'jp_marche_ja',
                'website_id' => '26',
                'group_id' => '38',
                'name' => 'Coke Japan Marche - Japanese',
                'sort_order' => '0',
                'is_active' => '1'
            ]
        ]
    ],
    'system' => [
        'default' => [
            'admin' => [
                'backend_google_sso' => [
                    'status' => '1',
                    'client_id' => '548780270365-lha2brr6j6on78f36m8brlq7l18766nu.apps.googleusercontent.com',
                    'auto_register_status' => '1',
                    'auto_register_email_matching_system' => 'in_domain',
                    'auto_register_email_matching_system_domain_list' => '{"_1584550489908_908":{"value":"fortyfour.com"}}',
                    'auto_register_email_matching_system_emails_list' => '[]'
                ],
                'security' => [
                    'session_lifetime' => '1800'
                ]
            ],
            'currency' => [
                'options' => [
                    'allow' => 'EGP,EUR',
                    'base' => 'EUR',
                    'default' => 'EUR'
                ]
            ],
            'general' => [
                'locale' => [
                    'code' => 'en_US',
                    'timezone' => 'America/New_York'
                ]
            ],
            'catalog' => [
                'price' => [
                    'scope' => '1'
                ],
                'search' => [
                    'engine' => 'elasticsearch7'
                ]
            ],
            'dev' => [
                'static' => [
                    'sign' => '1'
                ],
                'front_end_development_workflow' => [
                    'type' => 'server_side_compilation'
                ],
                'template' => [
                    'minify_html' => '1'
                ],
                'js' => [
                    'merge_files' => '0',
                    'minify_files' => '1',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/',
                        'cardinal_commerce' => '/v1/songbird',
                        'authorizenet_acceptjs' => '\\.authorize\\.net/v1/Accept'
                    ],
                    'move_script_to_bottom' => '0',
                    'session_storage_logging' => '0',
                    'translate_strategy' => 'dictionary',
                    'enable_js_bundling' => '0',
                    'enable_magepack_js_bundling' => '0'
                ],
                'css' => [
                    'minify_files' => '1',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/'
                    ],
                    'use_css_critical_path' => '0'
                ]
            ],
            'web' => [
                'secure' => [
                    'use_in_frontend' => 1,
                    'use_in_adminhtml' => 1
                ]
            ],
            'customer' => [
                'password' => [
                    'required_character_classes_number' => '3',
                    'minimum_password_length' => '8'
                ]
            ],
            'mpsocialshare' => [
                'general' => [
                    'enabled' => '0',
                    'thank_you' => '0',
                    'add_more_share' => [
                        'enabled' => 0
                    ]
                ]
            ]
        ],
        'stores' => [
            'default' => [
                'general' => [
                    'locale' => [
                        'code' => 'en_US'
                    ]
                ]
            ],
            'jp_marche_ja' => [
                'general' => [
                    'locale' => [
                        'code' => 'ja_JP'
                    ]
                ]
            ]
        ],
        'websites' => [
            'egypt_website' => [
                'general' => [
                    'country' => [
                        'default' => 'EG',
                        'allow' => 'EG'
                    ],
                    'locale' => [
                        'timezone' => 'Asia/Aden'
                    ]
                ],
                'currency' => [
                    'options' => [
                        'default' => 'EGP',
                        'allow' => 'EGP',
                        'base' => 'EGP'
                    ]
                ]
            ],
            'topo_chico_gr_website' => [
                'general' => [
                    'country' => [
                        'default' => 'GR',
                        'allow' => 'GR'
                    ],
                    'locale' => [
                        'timezone' => 'Europe/Athens'
                    ]
                ],
                'currency' => [
                    'options' => [
                        'default' => 'EUR',
                        'allow' => 'EUR',
                        'base' => 'EUR'
                    ]
                ],
                'wishlist' => [
                    'general' => [
                        'active' => '0'
                    ]
                ],
                'catalog' => [
                    'review' => [
                        'active' => '0'
                    ]
                ],
                'contact' => [
                    'email' => [
                        'recipient_email' => 'greece.cic@coca-cola.com'
                    ]
                ]
            ],
            'jp_marche' => [
                'design' => [
                    'theme' => [
                        'theme_id' => 'Coke/jp_marche'
                    ]
                ],
                'general' => [
                    'country' => [
                        'default' => 'JP'
                    ],
                    'locale' => [
                        'timezone' => 'Asia/Tokyo'
                    ]
                ],
                'currency' => [
                    'options' => [
                        'base' => 'JPY',
                        'default' => 'JPY',
                        'allow' => 'JPY'
                    ]
                ],
                'tax' => [
                    'defaults' => [
                        'country' => 'JP'
                    ]
                ],
                'payment' => [
                    'stripe_payments' => [
                        'card_icons' => 1,
                        'card_icons_specific' => 'visa,mastercard,jcb,amex,diners,discover'
                    ],
                    'free' => [
                        'order_status' => 'pending'
                    ]
                ],
                'newsletter' => [
                    'general' => [
                        'active' => '1'
                    ]
                ],
                'aw_sarp2' => [
                    'subscription_editing' => [
                        'can_edit_next_payment_date' => 0
                    ]
                ]
            ],
            'coke_uk' => [
                'general' => [
                    'store_information' => [
                        'name' => 'Coca Cola'
                    ]
                ],
                'design' => [
                    'theme' => [
                        'theme_id' => 'Coke/coke_eu'
                    ]
                ],
                'mpsocialshare' => [
                    'general' => [
                        'enabled' => '1'
                    ]
                ],
                'coke_whitelist' => [
                    'general' => [
                        'enabled' => '1',
                        'is_restriction_enabled' => '1',
                        'is_names_enabled' => '1'
                    ]
                ]
            ],
            'coke_eu' => [
                'general' => [
                    'store_information' => [
                        'name' => 'Coca Cola'
                    ]
                ],
                'design' => [
                    'theme' => [
                        'theme_id' => 'Coke/coke_eu'
                    ]
                ],
                'mpsocialshare' => [
                    'general' => [
                        'enabled' => '1'
                    ]
                ],
                'coke_whitelist' => [
                    'general' => [
                        'enabled' => '1',
                        'is_restriction_enabled' => '1',
                        'is_names_enabled' => '1'
                    ]
                ]
            ],
            'france_d2c' => [
                'currency' => [
                    'options' => [
                        'default' => 'EUR',
                        'allow' => 'EUR',
                        'base' => 'EUR'
                    ]
                ],
                'design' => [
                    'theme' => [
                        'theme_id' => 'Coke/france_d2c'
                    ]
                ],
                'checkout' => [
                    'postcode_restrictions' => [
                        'enabled' => '1'
                    ],
                    'options' => [
                        'enable_agreements' => '1'
                    ]
                ],
                'payment' => [
                    'braintree' => [
                        'title' => 'Carte de crédit',
                        'payment_action' => 'authorize_capture'
                    ],
                    'braintree_paypal' => [
                        'title' => 'PayPal',
                        'payment_action' => 'authorize_capture'
                    ]
                ]
            ]
        ]
    ],
    'modules' => [
        'Magento_Store' => 1,
        'Magento_AdminAnalytics' => 1,
        'Magento_AdminNotification' => 1,
        'Magento_AdminGwsConfigurableProduct' => 1,
        'Magento_AdminGwsStaging' => 1,
        'Magento_Directory' => 1,
        'Magento_AdobeIms' => 1,
        'Magento_AdobeImsApi' => 1,
        'Magento_AdobeStockAdminUi' => 1,
        'Magento_MediaGallery' => 1,
        'Magento_AdobeStockAssetApi' => 1,
        'Magento_AdobeStockClient' => 1,
        'Magento_AdobeStockClientApi' => 1,
        'Magento_AdobeStockImage' => 1,
        'Magento_Theme' => 1,
        'Magento_AdobeStockImageApi' => 1,
        'Magento_Eav' => 1,
        'Magento_Customer' => 1,
        'Magento_AdvancedPricingImportExport' => 1,
        'Magento_Rule' => 1,
        'Magento_Indexer' => 1,
        'Magento_Backend' => 1,
        'Magento_Amqp' => 1,
        'Magento_Config' => 1,
        'Magento_Variable' => 1,
        'Magento_Authorization' => 1,
        'Magento_User' => 1,
        'Magento_Cms' => 1,
        'Magento_Catalog' => 1,
        'Magento_AwsS3CustomerCustomAttributes' => 1,
        'Magento_GiftCardImportExport' => 1,
        'Magento_Widget' => 1,
        'Magento_ImportExport' => 1,
        'Magento_AdminAdobeIms' => 1,
        'Magento_Backup' => 1,
        'Magento_CatalogRule' => 1,
        'Magento_Quote' => 1,
        'Magento_SalesSequence' => 1,
        'Magento_Payment' => 1,
        'Magento_Sales' => 1,
        'Magento_Bundle' => 1,
        'Magento_GraphQl' => 1,
        'Magento_BundleImportExport' => 1,
        'Magento_BundleImportExportStaging' => 1,
        'Magento_CatalogInventory' => 1,
        'Magento_CacheInvalidate' => 1,
        'Magento_Checkout' => 1,
        'Magento_CardinalCommerce' => 1,
        'Magento_AdvancedCatalog' => 1,
        'Magento_Security' => 1,
        'Magento_CmsGraphQl' => 1,
        'Magento_EavGraphQl' => 1,
        'Magento_Search' => 1,
        'Magento_SalesArchive' => 1,
        'Magento_CatalogImportExport' => 1,
        'Magento_CatalogImportExportStaging' => 1,
        'Magento_StoreGraphQl' => 1,
        'Magento_CatalogInventoryGraphQl' => 1,
        'Magento_CatalogSearch' => 1,
        'Magento_CatalogPageBuilderAnalytics' => 1,
        'Magento_CatalogPageBuilderAnalyticsStaging' => 1,
        'Magento_CatalogUrlRewrite' => 1,
        'Magento_Ui' => 1,
        'Magento_CustomerCustomAttributes' => 1,
        'Magento_Msrp' => 1,
        'Magento_CatalogRuleGraphQl' => 1,
        'Magento_SalesRule' => 1,
        'Magento_Captcha' => 1,
        'Magento_Downloadable' => 1,
        'Magento_Staging' => 1,
        'Magento_GiftCard' => 1,
        'Magento_Wishlist' => 1,
        'Magento_CatalogGraphQl' => 1,
        'Magento_MediaStorage' => 1,
        'Magento_Robots' => 1,
        'Magento_ConfigurableProduct' => 1,
        'Magento_CheckoutAddressSearch' => 1,
        'Magento_GiftRegistry' => 1,
        'Magento_CheckoutAgreements' => 1,
        'Magento_CheckoutAgreementsGraphQl' => 1,
        'Magento_CheckoutStaging' => 1,
        'Magento_CloudComponents' => 1,
        'Magento_MediaGalleryUi' => 1,
        'Magento_CatalogCmsGraphQl' => 1,
        'Magento_CmsPageBuilderAnalytics' => 1,
        'Magento_CmsPageBuilderAnalyticsStaging' => 1,
        'Magento_VersionsCms' => 1,
        'Magento_CmsUrlRewrite' => 1,
        'Magento_CmsUrlRewriteGraphQl' => 1,
        'Magento_CompareListGraphQl' => 1,
        'Magento_Integration' => 1,
        'Magento_ConfigurableImportExport' => 1,
        'Magento_CatalogRuleConfigurable' => 1,
        'Magento_QuoteGraphQl' => 1,
        'Magento_ConfigurableProductSales' => 1,
        'Magento_PageCache' => 1,
        'Magento_Contact' => 1,
        'Magento_Cookie' => 1,
        'Magento_Cron' => 1,
        'Magento_Csp' => 1,
        'Magento_CurrencySymbol' => 1,
        'Magento_CustomAttributeManagement' => 1,
        'Magento_AdvancedCheckout' => 1,
        'Magento_Analytics' => 1,
        'Magento_CustomerBalance' => 1,
        'Magento_CustomerBalanceGraphQl' => 1,
        'Magento_CustomerSegment' => 1,
        'Magento_DownloadableGraphQl' => 1,
        'Magento_CustomerFinance' => 1,
        'Magento_CustomerGraphQl' => 1,
        'Magento_CustomerImportExport' => 1,
        'Magento_CatalogWidget' => 1,
        'Magento_DeferredTotalCalculating' => 1,
        'Magento_Deploy' => 1,
        'Magento_Developer' => 1,
        'Magento_Dhl' => 1,
        'Magento_BundleGraphQl' => 1,
        'Magento_DirectoryGraphQl' => 1,
        'Magento_ProductAlert' => 1,
        'Magento_CustomerDownloadableGraphQl' => 1,
        'Magento_DownloadableImportExport' => 1,
        'Magento_TargetRule' => 1,
        'Magento_AdvancedRule' => 1,
        'Magento_CatalogCustomerGraphQl' => 1,
        'Magento_AdvancedSearch' => 1,
        'Magento_Elasticsearch' => 1,
        'Magento_Elasticsearch6' => 1,
        'Magento_WebsiteRestriction' => 1,
        'Magento_ElasticsearchCatalogPermissionsGraphQl' => 1,
        'Magento_Email' => 1,
        'Magento_EncryptionKey' => 1,
        'Magento_Enterprise' => 1,
        'Magento_Fedex' => 1,
        'Magento_Tax' => 1,
        'Magento_GiftCardAccount' => 1,
        'Magento_GiftCardAccountGraphQl' => 1,
        'Magento_WishlistGraphQl' => 1,
        'Magento_Sitemap' => 1,
        'Magento_CatalogEvent' => 1,
        'Magento_GiftMessage' => 1,
        'Magento_GiftMessageGraphQl' => 1,
        'Magento_GiftMessageStaging' => 1,
        'Magento_UrlRewrite' => 1,
        'Magento_GiftRegistryGraphQl' => 1,
        'Magento_GiftWrapping' => 1,
        'Magento_GiftWrappingGraphQl' => 1,
        'Magento_GiftWrappingStaging' => 1,
        'Magento_GoogleAdwords' => 1,
        'Magento_GoogleAnalytics' => 1,
        'Magento_GoogleGtag' => 1,
        'Magento_GoogleOptimizer' => 1,
        'Magento_GoogleOptimizerStaging' => 1,
        'Magento_GoogleShoppingAds' => 1,
        'Magento_Banner' => 1,
        'Magento_AsyncOrder' => 1,
        'Magento_GraphQlCache' => 1,
        'Magento_GroupedProduct' => 1,
        'Magento_GroupedImportExport' => 1,
        'Magento_GroupedCatalogInventory' => 1,
        'Magento_GroupedProductGraphQl' => 1,
        'Magento_VisualMerchandiser' => 1,
        'Magento_RemoteStorage' => 1,
        'Magento_CatalogPermissions' => 1,
        'Magento_InstantPurchase' => 1,
        'Magento_CatalogAnalytics' => 1,
        'Magento_Inventory' => 1,
        'Magento_InventoryAdminUi' => 1,
        'Magento_InventoryAdvancedCheckout' => 1,
        'Magento_InventoryApi' => 1,
        'Magento_InventoryBundleImportExport' => 1,
        'Magento_InventoryBundleProduct' => 1,
        'Magento_InventoryBundleProductAdminUi' => 1,
        'Magento_InventoryBundleProductIndexer' => 1,
        'Magento_InventoryCatalog' => 1,
        'Magento_InventorySales' => 1,
        'Magento_InventoryCatalogAdminUi' => 1,
        'Magento_InventoryCatalogApi' => 1,
        'Magento_InventoryCatalogFrontendUi' => 1,
        'Magento_InventoryCatalogSearch' => 1,
        'Magento_InventoryCatalogSearchBundleProduct' => 1,
        'Magento_InventoryCatalogSearchConfigurableProduct' => 1,
        'Magento_ConfigurableProductGraphQl' => 1,
        'Magento_InventoryConfigurableProduct' => 1,
        'Magento_InventoryConfigurableProductFrontendUi' => 1,
        'Magento_InventoryConfigurableProductIndexer' => 1,
        'Magento_InventoryConfiguration' => 1,
        'Magento_InventoryConfigurationApi' => 1,
        'Magento_InventoryDistanceBasedSourceSelection' => 1,
        'Magento_InventoryDistanceBasedSourceSelectionAdminUi' => 1,
        'Magento_InventoryDistanceBasedSourceSelectionApi' => 1,
        'Magento_InventoryElasticsearch' => 1,
        'Magento_InventoryExportStockApi' => 1,
        'Magento_InventoryIndexer' => 1,
        'Magento_InventorySalesApi' => 1,
        'Magento_InventoryGroupedProduct' => 1,
        'Magento_InventoryGroupedProductAdminUi' => 1,
        'Magento_InventoryGroupedProductIndexer' => 1,
        'Magento_InventoryImportExport' => 1,
        'Magento_InventoryInStorePickupApi' => 1,
        'Magento_InventoryInStorePickupAdminUi' => 1,
        'Magento_InventorySourceSelectionApi' => 1,
        'Magento_InventoryInStorePickup' => 1,
        'Magento_InventoryInStorePickupGraphQl' => 1,
        'Magento_Shipping' => 1,
        'Magento_InventoryInStorePickupShippingApi' => 1,
        'Magento_InventoryInStorePickupQuoteGraphQl' => 1,
        'Magento_InventoryInStorePickupSales' => 1,
        'Magento_InventoryInStorePickupSalesApi' => 1,
        'Magento_InventoryInStorePickupQuote' => 1,
        'Magento_InventoryInStorePickupShipping' => 1,
        'Magento_InventoryInStorePickupShippingAdminUi' => 1,
        'Magento_Multishipping' => 1,
        'Magento_Webapi' => 1,
        'Magento_InventoryCache' => 1,
        'Magento_InventoryLowQuantityNotification' => 1,
        'Magento_Reports' => 1,
        'Magento_InventoryLowQuantityNotificationApi' => 1,
        'Magento_InventoryMultiDimensionalIndexerApi' => 1,
        'Magento_InventoryProductAlert' => 1,
        'Magento_InventoryQuoteGraphQl' => 1,
        'Magento_InventoryRequisitionList' => 1,
        'Magento_InventoryReservations' => 1,
        'Magento_InventoryReservationCli' => 1,
        'Magento_InventoryReservationsApi' => 1,
        'Magento_InventoryExportStock' => 1,
        'Magento_InventorySalesAdminUi' => 1,
        'Magento_InventoryGraphQl' => 1,
        'Magento_InventorySalesFrontendUi' => 1,
        'Magento_InventorySetupFixtureGenerator' => 1,
        'Magento_InventoryShipping' => 1,
        'Magento_InventoryShippingAdminUi' => 1,
        'Magento_InventorySourceDeductionApi' => 1,
        'Magento_InventorySourceSelection' => 1,
        'Magento_InventoryInStorePickupFrontend' => 1,
        'Magento_InventorySwatchesFrontendUi' => 1,
        'Magento_InventoryVisualMerchandiser' => 1,
        'Magento_InventoryWishlist' => 1,
        'Magento_Invitation' => 1,
        'Magento_JwtFrameworkAdapter' => 1,
        'Magento_JwtUserToken' => 1,
        'Magento_LayeredNavigation' => 1,
        'Magento_LayeredNavigationStaging' => 1,
        'Magento_Logging' => 1,
        'Magento_LoginAsCustomer' => 1,
        'Magento_LoginAsCustomerAdminUi' => 1,
        'Magento_LoginAsCustomerApi' => 1,
        'Magento_LoginAsCustomerAssistance' => 1,
        'Magento_LoginAsCustomerFrontendUi' => 1,
        'Magento_LoginAsCustomerGraphQl' => 1,
        'Magento_LoginAsCustomerLog' => 1,
        'Magento_LoginAsCustomerLogging' => 1,
        'Magento_LoginAsCustomerPageCache' => 1,
        'Magento_LoginAsCustomerQuote' => 1,
        'Magento_LoginAsCustomerSales' => 1,
        'Magento_LoginAsCustomerWebsiteRestriction' => 1,
        'Magento_Marketplace' => 1,
        'Magento_MediaContent' => 1,
        'Magento_MediaContentApi' => 1,
        'Magento_MediaContentCatalog' => 1,
        'Magento_MediaContentCatalogStaging' => 1,
        'Magento_MediaContentCms' => 1,
        'Magento_MediaContentSynchronization' => 1,
        'Magento_MediaContentSynchronizationApi' => 1,
        'Magento_MediaContentSynchronizationCatalog' => 1,
        'Magento_MediaContentSynchronizationCms' => 1,
        'Magento_AdobeStockAsset' => 1,
        'Magento_MediaGalleryApi' => 1,
        'Magento_MediaGalleryCatalog' => 1,
        'Magento_MediaGalleryCatalogIntegration' => 1,
        'Magento_MediaGalleryCatalogUi' => 1,
        'Magento_MediaGalleryCmsUi' => 1,
        'Magento_MediaGalleryIntegration' => 1,
        'Magento_MediaGalleryMetadata' => 1,
        'Magento_MediaGalleryMetadataApi' => 1,
        'Magento_MediaGalleryRenditions' => 1,
        'Magento_MediaGalleryRenditionsApi' => 1,
        'Magento_MediaGallerySynchronization' => 1,
        'Magento_MediaGallerySynchronizationApi' => 1,
        'Magento_MediaGallerySynchronizationMetadata' => 1,
        'Magento_AdobeStockImageAdminUi' => 1,
        'Magento_MediaGalleryUiApi' => 1,
        'Magento_AwsS3' => 1,
        'Magento_MessageQueue' => 1,
        'Magento_Weee' => 1,
        'Magento_MsrpConfigurableProduct' => 1,
        'Magento_MsrpGroupedProduct' => 1,
        'Magento_MsrpStaging' => 1,
        'Magento_MultipleWishlist' => 1,
        'Magento_SalesGraphQl' => 1,
        'Magento_InventoryInStorePickupMultishipping' => 1,
        'Magento_MysqlMq' => 1,
        'Magento_NewRelicReporting' => 1,
        'Magento_Newsletter' => 1,
        'Magento_NewsletterGraphQl' => 1,
        'Magento_OfflinePayments' => 1,
        'Magento_OfflineShipping' => 1,
        'Magento_BannerCustomerSegment' => 1,
        'Magento_PageBuilder' => 1,
        'Magento_AdminGws' => 1,
        'Magento_PageBuilderAnalytics' => 1,
        'Magento_CatalogStaging' => 1,
        'Magento_PageBuilderAdminGwsAdminUi' => 1,
        'Magento_PaymentGraphQl' => 1,
        'Magento_PaymentStaging' => 1,
        'Magento_Vault' => 1,
        'Magento_Paypal' => 1,
        'Magento_PaypalGraphQl' => 1,
        'Magento_PaypalOnBoarding' => 1,
        'Magento_Persistent' => 1,
        'Magento_PersistentHistory' => 1,
        'Magento_PricePermissions' => 1,
        'Magento_DownloadableStaging' => 1,
        'Magento_ProductVideo' => 1,
        'Magento_ProductVideoStaging' => 1,
        'Magento_PromotionPermissions' => 1,
        'Magento_BannerGraphQl' => 1,
        'Magento_QuoteAnalytics' => 1,
        'Magento_QuoteBundleOptions' => 1,
        'Magento_QuoteConfigurableOptions' => 1,
        'Magento_QuoteDownloadableLinks' => 1,
        'Magento_QuoteGiftCardOptions' => 1,
        'Magento_InventoryConfigurableProductAdminUi' => 1,
        'Magento_QuoteStaging' => 1,
        'Magento_ReCaptchaAdminUi' => 1,
        'Magento_ReCaptchaCheckout' => 1,
        'Magento_ReCaptchaCheckoutSalesRule' => 1,
        'Magento_ReCaptchaContact' => 1,
        'Magento_ReCaptchaCustomer' => 1,
        'Magento_ReCaptchaFrontendUi' => 1,
        'Magento_ReCaptchaGiftCard' => 1,
        'Magento_ReCaptchaInvitation' => 1,
        'Magento_ReCaptchaMigration' => 1,
        'Magento_ReCaptchaMultipleWishlist' => 1,
        'Magento_ReCaptchaNewsletter' => 1,
        'Magento_ReCaptchaPaypal' => 1,
        'Magento_ReCaptchaReview' => 1,
        'Magento_ReCaptchaSendFriend' => 1,
        'Magento_ReCaptchaStorePickup' => 1,
        'Magento_ReCaptchaUi' => 1,
        'Magento_ReCaptchaUser' => 1,
        'Magento_ReCaptchaValidation' => 1,
        'Magento_ReCaptchaValidationApi' => 1,
        'Magento_ReCaptchaVersion2Checkbox' => 1,
        'Magento_ReCaptchaVersion2Invisible' => 1,
        'Magento_ReCaptchaVersion3Invisible' => 1,
        'Magento_ReCaptchaWebapiApi' => 1,
        'Magento_ReCaptchaWebapiGraphQl' => 1,
        'Magento_ReCaptchaWebapiRest' => 1,
        'Magento_ReCaptchaWebapiUi' => 1,
        'Magento_RelatedProductGraphQl' => 1,
        'Magento_ReleaseNotification' => 1,
        'Magento_Reminder' => 1,
        'Magento_AwsS3GiftCardImportExport' => 1,
        'Magento_RemoteStorageCommerce' => 1,
        'Magento_InventoryLowQuantityNotificationAdminUi' => 1,
        'Magento_RequireJs' => 1,
        'Magento_ResourceConnections' => 1,
        'Magento_Review' => 1,
        'Magento_ReviewAnalytics' => 1,
        'Magento_ReviewGraphQl' => 1,
        'Magento_ReviewStaging' => 1,
        'Magento_Reward' => 1,
        'Magento_RewardGraphQl' => 1,
        'Magento_AdvancedSalesRule' => 1,
        'Magento_Rma' => 1,
        'Magento_RmaGraphQl' => 1,
        'Magento_RmaStaging' => 1,
        'Magento_ScheduledImportExport' => 1,
        'Magento_Rss' => 1,
        'Magento_SalesRuleStaging' => 1,
        'Magento_BannerPageBuilderAnalytics' => 1,
        'Magento_SalesAnalytics' => 1,
        'Magento_AsyncOrderGraphQl' => 1,
        'Magento_MultipleWishlistGraphQl' => 1,
        'Magento_SalesInventory' => 1,
        'Magento_CatalogRuleStaging' => 1,
        'Magento_RewardStaging' => 1,
        'Magento_BannerPageBuilder' => 1,
        'Magento_SampleData' => 1,
        'Magento_ScalableCheckout' => 1,
        'Magento_ScalableInventory' => 1,
        'Magento_ScalableOms' => 1,
        'Magento_AwsS3ScheduledImportExport' => 1,
        'Magento_Elasticsearch7' => 1,
        'Magento_SearchStaging' => 1,
        'Magento_CustomerAnalytics' => 1,
        'Magento_Securitytxt' => 1,
        'Magento_SendFriend' => 1,
        'Magento_SendFriendGraphQl' => 1,
        'Magento_InventoryInStorePickupSalesAdminUi' => 1,
        'Magento_AwsS3PageBuilder' => 1,
        'Magento_StagingGraphQl' => 1,
        'Magento_CatalogStagingGraphQl' => 1,
        'Magento_StagingPageBuilder' => 1,
        'Magento_CatalogPermissionsGraphQl' => 1,
        'Magento_UrlRewriteGraphQl' => 1,
        'Magento_Support' => 1,
        'Magento_Swagger' => 1,
        'Magento_SwaggerWebapi' => 1,
        'Magento_SwaggerWebapiAsync' => 1,
        'Magento_Swat' => 1,
        'Magento_Swatches' => 1,
        'Magento_SwatchesGraphQl' => 1,
        'Magento_SwatchesLayeredNavigation' => 1,
        'Magento_CatalogInventoryStaging' => 1,
        'Magento_TargetRuleGraphQl' => 1,
        'Magento_GiftCardStaging' => 1,
        'Magento_TaxGraphQl' => 1,
        'Magento_TaxImportExport' => 1,
        'Magento_GoogleTagManager' => 1,
        'Magento_ThemeGraphQl' => 1,
        'Magento_Translation' => 1,
        'Magento_TwoFactorAuth' => 0,
        'Magento_ElasticsearchCatalogPermissions' => 1,
        'Magento_Ups' => 1,
        'Magento_CatalogUrlRewriteStaging' => 1,
        'Magento_CatalogUrlRewriteGraphQl' => 1,
        'Magento_AsynchronousOperations' => 1,
        'Magento_Usps' => 1,
        'Magento_GroupedProductStaging' => 1,
        'Magento_PaypalCaptcha' => 1,
        'Magento_VaultGraphQl' => 1,
        'Magento_Version' => 1,
        'Magento_CmsStaging' => 1,
        'Magento_VersionsCmsPageCache' => 1,
        'Magento_VersionsCmsUrlRewrite' => 1,
        'Magento_VersionsCmsUrlRewriteGraphQl' => 1,
        'Magento_BundleStaging' => 1,
        'Magento_InventoryInStorePickupWebapiExtension' => 1,
        'Magento_WebapiAsync' => 1,
        'Magento_WebapiSecurity' => 1,
        'Magento_ConfigurableProductStaging' => 1,
        'Magento_CatalogStagingPageBuilder' => 1,
        'Magento_WeeeGraphQl' => 1,
        'Magento_WeeeStaging' => 1,
        'Magento_PageBuilderAdminAnalytics' => 1,
        'Magento_CheckoutAddressSearchGiftRegistry' => 1,
        'Magento_WishlistAnalytics' => 1,
        'Magento_WishlistGiftCard' => 1,
        'Magento_WishlistGiftCardGraphQl' => 1,
        'Magento_GiftCardGraphQl' => 1,
        'Aheadworks_Sarp2' => 1,
        'StripeIntegration_Payments' => 1,
        'Bounteous_CountryTranslation' => 1,
        'Bounteous_MaintenanceMode' => 1,
        'CokeEgypt_Customer' => 1,
        'CokeEurope_AddressAutocomplete' => 1,
        'CokeEurope_AgeRestriction' => 1,
        'CokeEurope_Catalog' => 1,
        'Coke_Cds' => 1,
        'Coke_Delivery' => 1,
        'CokeEurope_ContactModal' => 1,
        'CokeEurope_Currency' => 1,
        'CokeEurope_Customer' => 1,
        'CokeEurope_DataLayer' => 1,
        'Coke_Cms' => 1,
        'CokeEurope_NorthernIrelandVerification' => 1,
        'CokeEurope_PersonalizedProduct' => 1,
        'CokeEurope_ProductPopularity' => 1,
        'CokeEurope_StoreModifications' => 1,
        'CokeEurope_Stripe' => 1,
        'CokeEurope_Tax' => 1,
        'CokeEurope_Validations' => 1,
        'CokeFrance_Catalog' => 0,
        'CokeJapan_BottledCola' => 1,
        'CokeJapan_Checkout' => 1,
        'CokeJapan_Customer' => 1,
        'CokeJapan_Sarp2' => 1,
        'Coke_AdminTimezone' => 1,
        'Coke_Bundle' => 1,
        'Coke_CancelOrder' => 1,
        'CokeEurope_Cds' => 1,
        'CokeEurope_MegaMenu' => 1,
        'Coke_CompletedOrderQuestionnaire' => 1,
        'Coke_Contact' => 1,
        'Coke_ContactAgeRestrict' => 1,
        'Coke_SalesRule' => 1,
        'Coke_CustomerSoftDelete' => 1,
        'CokeEurope_Checkout' => 1,
        'Coke_DisableCheckoutEmail' => 1,
        'Coke_EmailAttachment' => 1,
        'Coke_Faq' => 1,
        'Coke_FaqCustom' => 1,
        'Coke_France' => 1,
        'Coke_InventoryBundleProduct' => 1,
        'Aheadworks_Sarp2Stripe' => 1,
        'Logicbroker_RetailerAPI' => 1,
        'Coke_Marche' => 1,
        'Coke_Whitelist' => 1,
        'Coke_OfflineShipping' => 1,
        'Coke_OpenGraph' => 1,
        'Coke_OrderGrid' => 1,
        'Coke_PageBuilderFaqs' => 1,
        'Coke_PageBuilderImageGrid' => 1,
        'Coke_Payment' => 1,
        'Coke_PersonalizedBottle' => 1,
        'Coke_PostcodeRestrictions' => 0,
        'Coke_PricesWithoutDecimals' => 1,
        'Coke_Customer' => 1,
        'Coke_Sarp2' => 1,
        'Coke_TaxImportExport' => 1,
        'Coke_User' => 1,
        'Coke_Validation' => 1,
        'Coke_OLNB' => 1,
        'Coke_WhitelistBulkOrder' => 1,
        'Coke_WhitelistEmail' => 1,
        'Enable_AddressLookup' => 1,
        'Fastly_Cdn' => 1,
        'FortyFour_AdminGws' => 1,
        'FortyFour_AgeRestriction' => 0,
        'FortyFour_Braintree' => 1,
        'FortyFour_Catalog' => 1,
        'FortyFour_CatalogInventory' => 0,
        'FortyFour_Cds' => 1,
        'FortyFour_CheckoutAgreements' => 1,
        'FortyFour_Config' => 1,
        'FortyFour_Csp' => 1,
        'FortyFour_DataLayer' => 1,
        'FortyFour_Directory' => 1,
        'FortyFour_Email' => 1,
        'FortyFour_FlatRateExtended' => 0,
        'FortyFour_InputMask' => 1,
        'FortyFour_LazySizes' => 1,
        'FortyFour_NewsletterSubscribeInterest' => 1,
        'Payfort_Fort' => 1,
        'FortyFour_Quote' => 1,
        'FortyFour_Sales' => 1,
        'FortyFour_SalesRule' => 1,
        'FortyFour_SalesSequence' => 1,
        'FortyFour_ShareSite' => 1,
        'FortyFour_Shipping' => 0,
        'FortyFour_ShippingAddressRestriction' => 1,
        'FortyFour_Store' => 0,
        'FortyFour_Tax' => 0,
        'FortyFour_Voucher' => 0,
        'FortyFour_Weee' => 1,
        'Iazel_RegenProductUrl' => 1,
        'Coke_Logicbroker' => 1,
        'MageMojo_Cron' => 1,
        'MageSuite_Magepack' => 1,
        'MagentoTwoTranslations_LanguageArSa' => 1,
        'Mageplaza_Core' => 1,
        'Mageplaza_SocialShare' => 1,
        'PayPal_Braintree' => 1,
        'PayPal_BraintreeGraphQl' => 1,
        'FortyFour_Payfort' => 1,
        'Sifuen_BackendGoogleSso' => 1,
        'Smile_ElasticsuiteAdminNotification' => 1,
        'Smile_ElasticsuiteCore' => 1,
        'Smile_ElasticsuiteCatalog' => 1,
        'Smile_ElasticsuiteCatalogGraphQl' => 1,
        'Smile_ElasticsuiteCatalogRule' => 1,
        'Smile_ElasticsuiteCatalogOptimizer' => 1,
        'Smile_ElasticsuiteTracker' => 1,
        'Smile_ElasticsuiteThesaurus' => 1,
        'Smile_ElasticsuiteSwatches' => 1,
        'Smile_ElasticsuiteIndices' => 1,
        'Smile_ElasticsuiteAnalytics' => 1,
        'Smile_ElasticsuiteVirtualCategory' => 1,
        'Coke_Japan' => 1,
        'Temando_ShippingRemover' => 1,
        'Zendesk_Zendesk' => 1
    ],
    'admin_user' => [
        'locale' => [
            'code' => [
                'en_US'
            ]
        ]
    ],
    'themes' => [
        'frontend/Magento/blank' => [
            'parent_id' => null,
            'theme_path' => 'Magento/blank',
            'theme_title' => 'Magento Blank',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/blank'
        ],
        'frontend/Magento/luma' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Magento/luma',
            'theme_title' => 'Magento Luma',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/luma'
        ],
        'adminhtml/Magento/backend' => [
            'parent_id' => null,
            'theme_path' => 'Magento/backend',
            'theme_title' => 'Magento 2 backend',
            'is_featured' => '0',
            'area' => 'adminhtml',
            'type' => '0',
            'code' => 'Magento/backend'
        ],
        'frontend/Coke/global' => [
            'parent_id' => 'Magento/luma',
            'theme_path' => 'Coke/global',
            'theme_title' => 'Coca-Cola Global Template',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/global'
        ],
        'frontend/Coke/egypt' => [
            'parent_id' => 'Coke/global',
            'theme_path' => 'Coke/egypt',
            'theme_title' => 'Coca-Cola Egypt',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/egypt'
        ],
        'frontend/Coke/egypt_rtl' => [
            'parent_id' => 'Coke/egypt',
            'theme_path' => 'Coke/egypt_rtl',
            'theme_title' => 'Coca-Cola Egypt Arabic(rtl)',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/egypt_rtl'
        ],
        'frontend/Coke/topochico' => [
            'parent_id' => 'Coke/global',
            'theme_path' => 'Coke/topochico',
            'theme_title' => 'Coca-Cola TopoChico Greece',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/topochico'
        ],
        'frontend/Coke/olnb' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Coke/olnb',
            'theme_title' => 'Coca-Cola OLNB',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/olnb'
        ],
        'frontend/Coke/coke_eu' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Coke/coke_eu',
            'theme_title' => 'Coca-Cola Europe',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/coke_eu'
        ],
        'frontend/Coke/olnb_turkey' => [
            'parent_id' => 'Coke/coke_eu',
            'theme_path' => 'Coke/olnb_turkey',
            'theme_title' => 'Coca-Cola OLNB Turkey',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/olnb_turkey'
        ],
        'frontend/Coke/france_d2c' => [
            'parent_id' => 'Coke/global',
            'theme_path' => 'Coke/france_d2c',
            'theme_title' => 'Coke France',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/france_d2c'
        ],
        'frontend/Coke/jp_marche' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Coke/jp_marche',
            'theme_title' => 'Coke Japan Marche',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Coke/jp_marche'
        ]
    ],
    'i18n' => [

    ]
];
