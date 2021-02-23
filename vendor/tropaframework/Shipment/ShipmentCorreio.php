<?php
namespace Tropaframework\Shipment;
use Tropaframework\Shipment\Model\Shipment;
use Tropaframework\Shipment\Model\Sender;
use Tropaframework\Shipment\Model\Package;
use Tropaframework\Shipment\Model\Recipient;
use Tropaframework\Shipment\Validate\ValidateCalcularFrete;
use Tropaframework\Shipment\Model\ShipmentMultiple;
use Tropaframework\Shipment\Model\Order;
use PhpSigep\Model\PreListaDePostagem;

require_once(dirname(__FILE__) . '/library/PhpSigepFPDF/PhpSigepFPDF.php');
require_once(dirname(__FILE__) . '/library/autoload.php');

/**
 * @NAICHE | Vitor Deco
 */
class ShipmentCorreio extends ShipmentAbstract
{	
	//método de envio
	const TYPE = "CORREIO";
	
	//user and password
	const USERNAME = "28679618000149";
	const PASSWORD = "wf70ka";
	const CODIGO_ADMINISTRATIVO = "17468728";
	const ADM_USERNAME = "alana@naiche.com.br";
	const ADM_PASSWORD = "a4Y45";
	
	/**
	 * tipo de acesso
	 */
	protected $accessData = null;
	
	public function __construct()
	{
		//tipo de acesso
		$this->accessData = new \PhpSigep\Model\AccessData();
		$this->accessData->setUsuario(self::USERNAME);
		$this->accessData->setSenha(self::PASSWORD);
		$this->accessData->setCodAdministrativo(self::CODIGO_ADMINISTRATIVO);
		
		//define as configurações
		$config = new \PhpSigep\Config();
		$config->setAccessData($this->accessData);
		
		//define o ambiente
		$config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
		
		//define opções de cache
		$config->setCacheOptions(
		    array(
		        'storageOptions' => array(
		            'enabled' => true,
		            'ttl' => 60*60*24*7, //"time to live" de 10 segundos
		        ),
		    )
		);
		
		//inicializar a API PhpSigep
		\PhpSigep\Bootstrap::start($config);
		
		//define o método de envio
		parent::__construct(self::TYPE);
	}
	
	public function setSandbox($bool)
	{
		if( $bool === true )
		{
			//tipo de acesso
			$this->accessData = new \PhpSigep\Model\AccessDataHomologacao();
			
			//define as configurações
			$config = new \PhpSigep\Config();
			$config->setAccessData($this->accessData);
			
			//define o ambiente
			$config->setEnv(\PhpSigep\Config::ENV_DEVELOPMENT);
			
			//inicializar a API PhpSigep
			\PhpSigep\Bootstrap::start($config);
		}
		 
		parent::setSandbox($bool);
		return $this;
	}
	
	public function calcularFrete(Package $package, Sender $sender, Recipient $recipient)
	{
		try {
			
			//validar campos
			$validate = new ValidateCalcularFrete();
			if( !$validate::isNotEmpty($sender->getCep()) )
			{
				throw new \Exception($validate::ERROR_REQUIRED_REMETENTE_CEP);
			}
			
			if( !$validate::isNotEmpty($recipient->getCep()) )
			{
				throw new \Exception($validate::ERROR_REQUIRED_DESTINATARIO_CEP);
			}
			
			$cond1 = !$validate::isNotEmpty($package->getAltura());
			$cond2 = !$validate::isNotEmpty($package->getComprimento());
			$cond3 = !$validate::isNotEmpty($package->getLargura());
			$cond4 = !$validate::isNotEmpty($package->getPeso());
			if( $cond1 || $cond2 || $cond3 || $cond4 )
			{
				throw new \Exception($validate::ERROR_REQUIRED);
			}
			
			//define o tipo do pacote padrão caso não tenha sido informado
			if( empty($package->getTipo()) )
			{
				$package->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
			}
			
			//serviços para consultar
// 			$pac = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_41068);
// 			$sedex = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096);
			$pac = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_REVERSO_CONTRATO_AGENCIA);
			$sedex = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_REVERSO_CONTRATO_AGENCIA);
			$sedex10 = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_10);
			$servicosPostagem = array($pac, $sedex, $sedex10);
			
			//definir dimensao
			$dimensao = new \PhpSigep\Model\Dimensao();
			$dimensao->setAltura($package->getAltura());
			$dimensao->setComprimento($package->getComprimento());
			$dimensao->setLargura($package->getLargura());
			$dimensao->setTipo($package->getTipo());
			
			//definir parâmetros para a consulta
			$params = new \PhpSigep\Model\CalcPrecoPrazo();
			$params->setAccessData($this->accessData);
			$params->setCepOrigem($sender->getCep());
			$params->setCepDestino($recipient->getCep());
			$params->setServicosPostagem($servicosPostagem);
			$params->setAjustarDimensaoMinima(true);
			$params->setDimensao($dimensao);
			$params->setPeso($package->getPeso());

			//faz a consulta na api
			$phpSigep = new \PhpSigep\Services\SoapClient\Real();
			$response = $phpSigep->calcPrecoPrazo($params)->getResult();
			//echo'<pre>'; print_r($response); exit;
			
			//resultado
			$result = new ShipmentMultiple();
			foreach( $response as $row )
			{
				//se não retornou valor continua
				if( $row->getValor() <= 0 ) continue;
				
				//define o nome do serviço (Pac, Sedex, Sedex 10)
				$servico = mb_strtolower($row->getServico()->getNome());
				$servico = (strpos($servico, 'sedex 10') !== false) ? self::ENTREGA_SUPERRAPIDO : ((strpos($servico, 'sedex') !== false) ? self::ENTREGA_RAPIDO : self::ENTREGA_ECONOMICO);
				
				//resultado item
				$item = new Shipment();
				$item->setServico($servico);
				$item->setPrazo($row->getPrazoEntrega());
				$item->setValor($row->getValor());
				$item->setObservacao($row->getErroMsg());
				$result->addItem($item);
			}
		
			//retorno
			return $result;
		
		} catch( \Exception $e ){
				
			throw new \Exception($e->getMessage(), $e->getCode());
				
		}
	}
	
	public function solicitarEtiqueta(Order $order)
	{
		try {
			
			//gerar etiquetas - params
			$params = new \PhpSigep\Model\SolicitaEtiquetas();
			$params->setQtdEtiquetas($order->countPackage());
			$params->setServicoDePostagem($this->getServicoDePostagem($order->getServico()));
			$params->setAccessData($this->accessData);
			
			//gerar etiquetas com código de rastreio
			$phpSigep = new \PhpSigep\Services\SoapClient\Real();
			$etiquetaSolicitada = $phpSigep->solicitaEtiquetas($params);
			$etiquetas = $etiquetaSolicitada->getResult();
			
			//vincular códigos das etiquetas geradas
			foreach( $order->getPackage() as $key=>$package )
			{
				if( empty($package->getRastreio()) )
				{
					$etiqueta = $etiquetas[$key];
					$package->setRastreio($etiqueta->getEtiquetaComDv());
				}
			}
			
			return $order;
			
		} catch( \Exception $e ){
			
			throw new \Exception($e->getMessage(), $e->getCode());
			
		}
	}
	
	public function imprimirEtiqueta(Order $order)
	{
		try {
			
			//gerar PDF com as etiquetas
			$plp = $this->paramsSolicitarEtiqueta($order);
			$logoFile = 'http://' . $_SERVER['HTTP_HOST'] . '/assets/bag365/img/logos/bag365-dark.png';
			$pdf = new \PhpSigep\Pdf\CartaoDePostagem2016($plp, time(), $logoFile);
			return $pdf->render();
				
		} catch( \Exception $e ){
				
			throw new \Exception($e->getMessage(), $e->getCode());
				
		}
	}
	
	public function rastrearPedido($codigo)
	{
// 		$accessData = new \PhpSigep\Model\AccessDataHomologacao();
// 		$accessData->setUsuario('ECT'); //Usuário e senha para teste passado no manual
// 		$accessData->setSenha('SRO');
	
		try {
			
			//definir etiqueta
			$etiqueta = new \PhpSigep\Model\Etiqueta();
			$etiqueta->setEtiquetaComDv($codigo);
		
			//rastrear
			$params = new \PhpSigep\Model\RastrearObjeto();
			$params->setAccessData($this->accessData);
			$params->addEtiqueta($etiqueta);
			$phpSigep = new \PhpSigep\Services\SoapClient\Real();
			$result = $phpSigep->rastrearObjeto($params)->getResult();
			echo'<pre>'; print_r($result); exit;
			
		} catch( \Exception $e ){
		
			throw new \Exception($e->getMessage(), $e->getCode());
		
		}
	}
	
	/**
	 * @param Order $order
	 * @return PreListaDePostagem
	 */
	private function paramsSolicitarEtiqueta(Order $order)
	{
		//PLP
		$plp = new \PhpSigep\Model\PreListaDePostagem();
		$plp->setAccessData($this->accessData);
		
		//definir destinatario
		$destinatario = new \PhpSigep\Model\Destinatario();
		$destinatario->setNome($order->getDestinatario()->getNome());
		$destinatario->setLogradouro($order->getDestinatario()->getLogradouro());
		$destinatario->setNumero($order->getDestinatario()->getNumero());
		$destinatario->setComplemento($order->getDestinatario()->getComplemento());
		$destino = new \PhpSigep\Model\DestinoNacional();
		$destino->setCep($order->getDestinatario()->getCep());
		$destino->setBairro($order->getDestinatario()->getBairro());
		$destino->setCidade($order->getDestinatario()->getCidade());
		$destino->setUf($order->getDestinatario()->getEstado());
		
		//definir remetente
		$remetente = new \PhpSigep\Model\Remetente();
		$remetente->setNome($order->getRemetente()->getNome());
		$remetente->setCep($order->getRemetente()->getCep());
		$remetente->setLogradouro($order->getRemetente()->getLogradouro());
		$remetente->setNumero($order->getRemetente()->getNumero());
		$remetente->setComplemento($order->getRemetente()->getComplemento());
		$remetente->setBairro($order->getRemetente()->getBairro());
		$remetente->setCidade($order->getRemetente()->getCidade());
		$remetente->setUf($order->getRemetente()->getEstado());
		$plp->setRemetente($remetente);
		
		//loop nos pacotes
		$encomendas = array();
		foreach( $order->getPackage() as $package )
		{
			//definir dimensao do pacote
			$dimensao = new \PhpSigep\Model\Dimensao();
			$dimensao->setAltura($package->getAltura());
			$dimensao->setLargura($package->getLargura());
			$dimensao->setComprimento($package->getComprimento());
			$dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
			
			//definir etiqueta
			$etiqueta = new \PhpSigep\Model\Etiqueta();
			$etiqueta->setEtiquetaComDv($package->getRastreio());
			
			//serviço adicional
			$servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
			$servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
			$servicoAdicional->setValorDeclarado($package->getValor()); //se não tiver valor declarado informar 0 (zero)
			
			//definir encomenda
			$peso = ($package->getPeso() < 1) ? 1 : $package->getPeso();
			$encomenda = new \PhpSigep\Model\ObjetoPostal();
			$encomenda->setServicosAdicionais(array($servicoAdicional));
			$encomenda->setDestinatario($destinatario);
			$encomenda->setDestino($destino);
			$encomenda->setDimensao($dimensao);
			$encomenda->setEtiqueta($etiqueta);
			$encomenda->setPeso($peso); //em gramas (0.500 = 500g)
			$encomenda->setServicoDePostagem($this->getServicoDePostagem($order->getServico()));
			$encomendas[] = $encomenda;
		}
		$plp->setEncomendas($encomendas);
		//echo'<pre>'; print_r($plp); exit;
		
		return $plp;
	}
	
	private function getServicoDePostagem($servico)
	{
		$pac = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_REVERSO_CONTRATO_AGENCIA);
		$sedex = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_REVERSO_CONTRATO_AGENCIA);
		$sedex10 = new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_10);
	
		$servico = (strpos($servico, self::ENTREGA_SUPERRAPIDO) !== false) ? $sedex10 : ((strpos($servico, self::ENTREGA_RAPIDO) !== false) ? $sedex : $pac);
		return $servico;
	}
	
}