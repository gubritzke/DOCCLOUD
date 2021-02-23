<?php
namespace Tropaframework\Facebook;

class Tracking
{
	public static function viewDetail($head, $produto)
    {
   		$params = array(
			'content_name' => $produto->produto,
   			'content_category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
   			'content_ids' => array($produto->id_produto_loja),
   			'content_type' => 'product',
   			'value' => $produto->preco,
   			'currency' => 'BRL',
	   	);
   		
   		$head->addFacebookPixelCodeTrack("ViewContent", $params);
    }
	
	public static function initiateCheckout($head)
	{
		//adicionar track do facebook
		$head->addFacebookPixelCodeTrack("InitiateCheckout");
	}
	
	public static function addToCart($head, $produtos)
    {
   		foreach( $produtos as $produto )
   		{
   			//adicionar track do facebook
	   		$facebook = array(
	   			'content_name' => $produto->produto,
	   			'content_category' => \Naicheframework\Helper\Convert::breakText($produto->produto),
	   			'content_ids' => array($produto->id_produto_loja),
	   			'content_type' => 'product',
	   			'value' => $produto->subtotal,
	   			'currency' => 'BRL'
	   		);
	   		$head->addFacebookPixelCodeTrack("AddToCart", $facebook);
   		}
    }
	
	public static function purchase($head, $produtos, $pedido)
    {
    	$facebookParams = array();
    	foreach( $produtos as $produto )
    	{
    		$facebookParams[] = array(
    			'id' => $produto->id_produto_loja,
    			'quantity' => $produto->quantidade,
    			'item_price' => $produto->preco
    		);
    	}
    	
    	//adicionar track do facebook
		$facebook = array(
    		'contents' => $facebookParams,
    		'content_type' => 'product',
    		'value' => $pedido->total,
    		'currency' => 'BRL'
    	);
		$head->addFacebookPixelCodeTrack("Purchase", $facebook);
    }
}