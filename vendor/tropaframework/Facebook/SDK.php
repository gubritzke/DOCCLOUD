<?php
namespace Tropaframework\Facebook;
require_once __DIR__ . '/SDK/autoload.php';

class SDK
{
	private $fb;
	
	public function __construct($sandbox = false)
    {
    	$this->fb = new \Facebook\Facebook([
    		'app_id' => '1517555758313897',
    		'app_secret' => '363bb49c3ae94b55b942478f900bda6a',
    		'default_graph_version' => 'v2.10',
    	]);
    }
    
    public function setSandbox($bool)
    {
    	if( $bool === true )
    	{
    		$this->fb = new \Facebook\Facebook([
    			'app_id' => '342526202893278',
    			'app_secret' => 'c93bc353aad09795f515b925f413100e',
    			'default_graph_version' => 'v2.10',
    		]);
    	}
    }
    
    public function loginRedirect()
    {
    	//definir a url de retorno
    	$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
    	$redirectUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/login/facebook';
    	
    	//solicitar permissões
		$scope = array('email');
    	
    	//link de redirecionamento
    	$helper = $this->fb->getRedirectLoginHelper();
		$loginUrl = $helper->getLoginUrl($redirectUrl, $scope);
		
		//retorna a url de login do facebook
		return $loginUrl;
    }
    
    public function get()
    {
    	try {
    		
    		//recuperar token de acesso 
    		$helper = $this->fb->getRedirectLoginHelper();
    		$accessToken = $helper->getAccessToken();
    		if( empty($accessToken) ) throw new \Exception('Acesso inválido!');
    		
    		//recuperar informações do usuário
    		$response = $this->fb->get('/me?fields=id,name,link,gender,locale,cover,picture.type(large),email', $accessToken);
    		return $response->getGraphUser()->asArray();
    		
    	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
    		
    		// When Graph returns an error
    		throw new \Exception('Graph returned an error: ' . $e->getMessage());
    		
    	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
    		
    		// When validation fails or other local issues
    		throw new \Exception('Facebook SDK returned an error: ' . $e->getMessage());
    		
    	}
    }
}