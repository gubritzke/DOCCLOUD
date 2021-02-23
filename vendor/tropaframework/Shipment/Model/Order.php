<?php
namespace Tropaframework\Shipment\Model;


/**
 * @author: Vitor Deco
 */
class Order extends ModelAbstract
{
	/**
	 * grupo do pedido
	 * @var string
	 */
	protected $group;

	/**
	 * serviço de entrega dos pacotes (Econômica, Rápida, Super Rápida)
	 * @var string
	 */
	protected $servico;
	
	/**
	 * @var Sender
	 */
	protected $remetente;
	
	/**
	 * @var Recipient
	 */
	protected $destinatario;
	
	/**
	 * array com itens/produtos do pedido
	 * @var Item
	 */
	protected $itens = array();
	
	/**
	 * array de pacotes
	 * @var Package
	 */
	protected $pacotes = array();
	
    public function getGroup()
    {
    	return $this->group;
    }
    
    public function setGroup($value)
    {
    	$this->group = $value;
    }

    public function getServico()
    {
    	return $this->servico;
    }
    
    public function setServico($value)
    {
    	$this->servico = $value;
    }
    
	public function getRemetente()
    {
    	return $this->remetente;
    }
    
    public function setRemetente(Sender $value)
    {
    	$this->remetente = $value;
    }
    
    public function getDestinatario()
    {
    	return $this->destinatario;
    }
    
    public function setDestinatario(Recipient $value)
    {
    	$this->destinatario = $value;
    }
    
    /**
     * @return Item
     */
    public function getItem()
    {
    	return $this->itens;
    }
    
    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
    	$this->itens[] = $item;
    }
    
    /**
     * @return int
     */
    public function countItem()
    {
    	return count($this->itens);
    }
    
    /**
     * @return Package
     */
    public function getPackage()
    {
    	return $this->pacotes;
    }
    
    /**
     * @return Package
     */
    public function getPackageById($value)
    {
    	foreach( $this->pacotes as $package )
    	{
    		if( in_array($value, $package->getId()) )
    		{
    			return $package;
    		}
    	}
    	
    	$package = new Package();
    	return $package;
    }
    
    /**
     * @param Package $package
     */
    public function addPackage(Package $package)
    {
		$this->pacotes[] = $package;
    }
    
    /**
     * @return int
     */
    public function countPackage()
    {
    	return count($this->pacotes);
    }
    
    /**
     * atualiza um pacote validando os seus limites
     * 
     * Peso: máx 30kg (sedex 10 é 10kg)
     * Comprimento: min. 16cm e máx. 105cm
     * Largura: min. 11cm e máx 105cm
     * Altura: min. 2cm e máx. 105cm
     * Soma máxima das dimensões para caixa: máx. 200cm
     * 
     * @param Package $package
     */
    public function mergePackage(Package $package)
    {
    	//define o pacote para agrupar
    	//Dimensões de uma mala pequena
    	//Altura: 55 cm
    	//Largura: 36 cm
    	//Comprimento: 21 cm
    	
    	$alturaLimite = 50;
    	foreach( $this->pacotes as $pacote )
    	{
    		$cond1 = ($pacote->getAltura() <= $alturaLimite);
    		$cond2 = ($package->getAltura() <= $alturaLimite);
    		if( $cond1 && $cond2 )
    		{
    			$packageToMerge = $pacote;
    		}
    	}
    	
    	//atualizar o pacote existente
    	if( isset($packageToMerge) )
    	{
    		//atualizar quantidade de produtos no pacote
    		foreach( $package->getId() as $id ) $packageToMerge->addId($id);
    		
    		//atualizar preço dos produtos no pacote
    		$value = $packageToMerge->getValor() + $package->getValor();
    		$packageToMerge->setValor($value);
    		
    		//atualizar peso dos produtos no pacote
    		$value = $packageToMerge->getPeso() + $package->getPeso();
    		$packageToMerge->setPeso($value);
    		
    		//manter a altura do maior pacote
    		if( $packageToMerge->getAltura() < $package->getAltura() )
    		{
    			$packageToMerge->setAltura($package->getAltura());
    		}
    		
    		//manter a largura do maior pacote
    		if( $packageToMerge->getLargura() < $package->getLargura() )
    		{
    			$packageToMerge->setLargura($package->getLargura());
    		}
    		
    		//manter o comprimento do maior pacote
    		if( $packageToMerge->getComprimento() < $package->getComprimento() )
    		{
    			$packageToMerge->setComprimento($package->getComprimento());
    		}
			
    		//atualizar as entregas
    		$shipmentMultiple = $package->getShipments();
    		if( !empty($shipmentMultiple) && $shipmentMultiple->itemLenght() )
    		{
    			$packageToMerge->setShipments($shipmentMultiple);
    		}
    		
    	} else {
    		
    		//adicionar um novo pacote
    		$this->addPackage($package);
    		
    	}
    	
    }
    
    /**
     * @return ShipmentMultiple
     */
    public function getShipments()
    {
    	//resultado
    	$shipmentMultiple = new ShipmentMultiple();
    	
    	//loop em todas as entregas calculadas
    	foreach( $this->pacotes as $package )
    	{
    		foreach( $package->getShipments()->getItens() as $row )
    		{
    			//verificar se o serviço já existe
    			$item = $shipmentMultiple->getItemByService($row->getServico());
    			
    			if( empty($item->getServico()) )
    			{
    				//adicionar item
    				$item = new Shipment();
    				$item->setServico($row->getServico());
    				$item->setPrazo($row->getPrazo());
    				$item->setValor($row->getValor());
    				$item->setObservacao($row->getObservacao());
    				$shipmentMultiple->addItem($item);
    				
    			} else {
    				
    				//definir novo prazo
    				$value = ($item->getPrazo() >= $row->getPrazo()) ? $item->getPrazo() : $row->getPrazo();
    				$item->setPrazo($value);
    				
    				//definir novo valor
    				$value = ($item->getValor() + $row->getValor());
    				$item->setValor($value);
    			}
    		}
    	}
    	
    	return $shipmentMultiple;
    }
    
    /**
     * @return number
     */
    public function countIds()
    {
    	$count = 0;
    	foreach( $this->pacotes as $package )
    	{
    		$count += (int)$package->countIds();
    	}
    	return $count;
    }
}