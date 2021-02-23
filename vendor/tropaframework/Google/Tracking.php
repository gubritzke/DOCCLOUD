<?php
namespace Tropaframework\Google;

class Tracking
{
	public static function impressions($head, $produtos)
    {
		$params = array();
		$i = 1;
   		foreach( $produtos as $produto )
   		{
	   		$params[] = array(
   				'name' => $produto->produto,
   				'id' => $produto->id_produto_loja,
   				'price' => $produto->preco,
   				'category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
	   			'list' => 'Search Results',
       			'position' => $i++,
	   		);
   		}
    	
   		$array = array(
   			'ecommerce' => array(
   				'currencyCode' => 'BRL',
   				'impressions' => $params
   			)
   		);
   		$head->addGoogleTagManagerPush($array);
    }
	
	public static function productClick($head, $produto)
    {
   		$params = array(
			'name' => $produto->produto,
   			'id' => $produto->id_produto_loja,
   			'price' => $produto->preco,
   			'category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
			//'position' => 1
	   	);
   		
   		$array = array(
	   		'event' => 'productClick',
	   		'ecommerce' => array(
		   		'click' => array(
			   		'actionField' => array(
			   			'list' => 'Search Results', //Optional list property.
			   		),
			   		'products' => array($params)
		   		)
	   		),
   		);
   		$head->addGoogleTagManagerPush($array);
    }

	public static function viewDetail($head, $produto)
    {
		$filtros = array_column($produto->filtros, 'filtro', 'filtro_tipo');
		
    	$array = array(
    		"pageCategory" => "Product",
    		"pageDepartment" => $filtros['Departamento'],
    		"pageUrl" => "https://" . $_SERVER["HTTP_HOST"] . "/produto/" . $produto->produto_slug,
    		"pageTitle" => "Bag365 " . $produto->produto,
    		//"skuStockOutFromShelf" => array(),
    		//"skuStockOutFromProductDetail" => array(),
    		//"shelfProductIds" => array(),
    		//"accountName" => "baherimports",
    		//"pageFacets" => array(),
    		"productId" => $produto->id_produto_loja,
    		//"productReferenceId" => "1001030037",
    		"productEans" => array($produto->ean),
    		//"skuStocks" => array("1270969645" => 2868),
	    	"productName" => $produto->produto,
	    	//"productBrandId" => 1312542035,
	    	"productBrandName" => $filtros['Marca'],
	    	//"productDepartmentId" => 1783105294,
	    	"productDepartmentName" => $filtros['Departamento'],
	    	//"productCategoryId" => 314951373,
	    	"productCategoryName" => $filtros['Categoria'],
	    	"productPriceFrom" => $produto->preco_de,
	    	"productPriceTo" => $produto->preco,
    	);
    	
   		$head->addGoogleTagManagerPush($array);
    }
    
	public static function addToCart($head, $produtos)
    {
   		$params = array();
   		foreach( $produtos as $produto )
   		{
	   		$params[] = array(
   				'name' => $produto->produto,
   				'id' => $produto->id_produto_loja,
   				'price' => $produto->subtotal,
   				'category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
   				'quantity' => (int)$produto->quantidade
	   		);
   		}
   		
   		$google = array(
   			'event' => 'AddToCart',
   			'ecommerce' => array(
   				'currencyCode' => 'BRL',
   				'add' => array(
   					'products' => $params
   				)
   			)
   		);
   		$head->addGoogleTagManagerPush($google);
    }
    
	public static function removeFromCart($head, $produtos)
    {
   		$params = array();
   		foreach( $produtos as $produto )
   		{
	   		//adicionar event do google
	   		$params[] = array(
   				'name' => $produto->produto,
   				'id' => $produto->id_produto_loja,
   				'price' => $produto->subtotal,
   				'category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
   				//'variant' => 'Gray',
   				'quantity' => (int)$produto->quantidade
	   		);
   		}
   		
   		//adicionar event do google
   		$google = array(
   			'event' => 'removeFromCart',
   			'ecommerce' => array(
   				'remove' => array( //'remove' actionFieldObject measures.
   					'products' => $params //removing a product to a shopping cart.
   				)
   			)
   		);
   		$head->addGoogleTagManagerPush($google);
    }
	
	public static function checkout($head, $produtos, $step=1)
    {
    	$products = array();
    	foreach( $produtos as $produto )
    	{
    		$products[] = array(
    			'id' => $produto->id_produto_loja,
    			'name' => $produto->produto,
    			'category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
    			'price' => $produto->preco,
    			'quantity' => (int)$produto->quantidade,
    		);
    	}
    	
		//adicionar event do google
		$google = array(
			'ecommerce' => array(
				'checkout' => array(
					'actionField' => array(
						'step' => $step, 
					),
					'products' => $products
				)
			),
			
			/*
			"visitorType" => "new customer",
			"visitorContactInfo" => array(),
			"orderFormTax" => null,
			"orderFormShipping" => null,
			"orderFormShippingMethod" => array(),
			"orderFormPromoCode" => null,
			"orderFormTotal" => 3999,
			"orderFormProducts" => array(
				array(
					"id" => "1294576391",
					"name" => "Cobre-Leito/Colcha Casal Dupla Face Owl Up - 3Pçs",
					"sku" => "1270969645",
					"skuName" => "Cobre-Leito/Colcha Casal Dupla Face Owl Up - 3Pçs",
					"seller" => "Casa Baher",
					"sellerId" => "1",
					"brand" => "BAHER",
					"brandId" => "1312542035",
					"isGift" => false,
					"category" => "Cobre-leito casal",
					"categoryId" => "314951373",
					"originalPrice" => 39.99,
					"price" => 39.99,
					"sellingPrice" => 39.99,
					"tax" => 0,
					"quantity" => 1,
					"components" => array(),
				)
			)
			*/
   		);
		$head->addGoogleTagManagerPush($google);
    }
    
	public static function purchase($head, $produtos, $pedido)
    {
    	$params = array();
    	foreach( $produtos as $produto )
    	{
    		$params[] = array(
    			'sku' => $produto->id_produto_loja,
    			'name' => $produto->descricao,
    			'price' => $produto->preco,
    			'quantity' => $produto->quantidade,
    			'category' => \Naicheframework\Helper\Convert::breakText($produto->descricao),
    		);
    	}
    	
		//adicionar event do google
		$google = array(
			'event' => 'orderPlaced',
			'transactionId' => $pedido->id_pedido,
			'transactionTotal' => ($pedido->total + $pedido->frete_valor),
			'transactionTax' => $pedido->frete_valor,
			'transactionProducts' => $params,
   		);
		$head->addGoogleTagManagerPush($google);
    }
}