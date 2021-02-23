<?php
namespace Tropaframework\Google;

use Zend\Config\Writer\Xml;

class Shopping
{
    protected $xml;
    protected $basicUrl;
    protected $title;
    protected $link;
    protected $description;
    
	public function __construct($basicUrl)
	{
	   $this->setBasicUrl($basicUrl);
	   $this->setXml();
	}

	/**
     * @return the $basicUrl
     */
    public function getBasicUrl()
    {
        return $this->basicUrl;
    }

    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return the $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return the $description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getXml(){
        return $this->xml->asXML();
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public function setXml()
    {
        $this->xml = new \SimpleXMLElement('<?xml version="1.0"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"></rss>');
    }
    
    /**
     * @param field_type $basicUrl
     */
    public function setBasicUrl($basicUrl)
    {
        $this->basicUrl = $basicUrl;
    }

    /**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
       // $this->title->addChild('title');
    }

    /**
     * @param field_type $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param field_type $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

	public function setProdutos($produtos = array()){
	    $this->xml->addChild('title',$this->getTitle());
	    $this->xml->addChild('link',$this->getLink());
	    $this->xml->addChild('description',$this->getDescription());
	    
	    $channel = $this->xml->addChild('channel');
	    
	    foreach ($produtos as $row){
	        $item = $channel->addChild('item');
	        
	        //echo '<pre>'; print_r($produtos); exit;
	        
	        $item->addChild("g:id",$row->id_produto,'g');
	        $item->addChild('g:title',$row->produto,'g');
	        $item->addChild('g:description',$row->descricao,'g');
	        $item->addChild('g:link',$this->basicUrl.$row->produto_slug,'g');
	        $item->addChild('g:image_link',$row->imagem_1,'g');
	        $item->addChild('g:availability','in stock','g');
	        $item->addChild('g:price', $row->preco.' BRL','g');
	        $item->addChild('g:gtin', $row->ean,'g');

	        
	        $item->addChild('g:condition', 'new','g');
	        $item->addChild('g:adult', 'no','g');
	        $item->addChild('g:is_bundle', 'no','g');
	        $item->addChild('g:color', $row->filtros['Cor']->filtro,'g');
	        $item->addChild('g:brand', $row->filtros['Marca']->filtro,'g');
	        $item->addChild('g:google_product_category','Vestuário e acessórios > Bolsas','g');
	        $item->addChild('g:item_group_id',$row->ean,'g');
	        
	        if($row->filtros['Departamento']->filtro_slug == 'infantil'){
	            $item->addChild('g:age_group','kids','g');
	        }else{
	            $item->addChild('g:age_group','adult','g');
	        }
	        
	        
	        if($row->filtros['Gênero']->filtro == 'Masculino'){
	           $item->addChild('g:gender', 'male','g');
	        }elseif($row->filtros['Gênero']->filtro == 'Feminino'){
	           $item->addChild('g:gender', 'female','g');
	        }else{
	           $item->addChild('g:gender', 'unisex','g');
	        }
	        
	        
	    }
	    
	    
	}
	
	public function save(){
// 	    //echo '<pre>'; print_r($this->xml->getDocNamespaces()); exit;
	    
 	    $xml = $this->xml->asXML();
	    
 	    $test =  str_replace('xmlns:g="g"', '', $xml);
	    
 	    
 	    //$open = fopen('public/shopping/shopping.xml', 'a');
 	    file_put_contents('public/shopping/shopping.xml', $test );
 	    //echo '<pre>'; print_r($test); exit;
	    
 	    
//  	    $xml = new \SimpleXMLElement($test);
//  	    $xml->asXML();
	    
	    //$this->xml->saveXML();
	}
	
	
	
}