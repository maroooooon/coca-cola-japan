<?php
declare(strict_types=1);

namespace CokeEurope\DataLayer\Plugin;

use CokeEurope\DataLayer\ViewModel\Data as DataLayer;
use Magento\CatalogWidget\Block\Product\ProductsList;

class AddDatalayerToProductWidget
{

	private DataLayer $datalayer;

	public function __construct(
		DataLayer $datalayer
	) {
		$this->datalayer = $datalayer;
	}

    public function beforeToHtml(ProductsList $productsList): void
    {
        $productsList->setData('datalayer_config', $this->datalayer);
    }
}
