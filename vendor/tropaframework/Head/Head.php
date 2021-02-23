<?php
namespace Tropaframework\Head;

/**
 * @NAICHE | Kaique Steck | Vitor Deco
 * Classe que facilita o Head do html e melhora o SEO
 */
class Head
{
	protected $pasta;
	protected $css;
	protected $js;
	protected $meta;
	protected $title;
	protected $version;
	protected $minifica;
	protected $config_host;
	protected $getServiceLocator;
	
	public function __construct($getServiceLocator, $modulo, $version='1.0', $largura='750', $charset='UTF-8')
	{
		// PEGA TODAS AS INFOS NECESSÃ�RIAS PARA INSERIR SCRIPT/META/LINK
		$this->getServiceLocator = $getServiceLocator;
		
		// FUNÃ‡ÃƒO DO ZEND QUE DA UM APPEND NO SCRIPT/LINK/META
		$this->css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$this->js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$this->meta = $this->getServiceLocator->get('ViewHelperManager')->get('headMeta');
		$this->title = $this->getServiceLocator->get('viewHelperManager')->get('headTitle');
		$this->version = '?version=' . $version;
		
		// INSERE A NAICHE COMO AUTHOR NO META
		$this->setMeta('author','http://naiche.com.br');
		
		// VERIFICAR SE ESTÃ� EM DESENVOLVIMENTO
		$this->config_host = $this->getServiceLocator->get('config')['config_host']['env'];
		
		// GET INFOS DA APLICAÃ‡ÃƒO
		$application = $this->getServiceLocator->get($modulo);
		
		$matches  	= $application->getMvcEvent()->getRouteMatch();
		
		$module		= $matches->getParam('__NAMESPACE__');
		$module 	= explode('\Controller',$module);
		$module 	= $module[0];
		
		$controller	= $matches->getParam('__CONTROLLER__');
		$action		= $matches->getParam('action');
		
		// DIZ SE VAI MINIFICAR LOCAL OU NÃƒO
// 		$this->minifica = $this->config_host != 'local' ? 'minify/' : '';
		$this->minifica = null;
		
		$this->pasta = strtolower( $module ).'/';
		
		// START TODA CSS
		$this->css($controller, $action);
		
		// START TODA JS
		$this->js($controller, $action);
		
		// START CHARSETA NO META
		$this->metaCharset($charset);
		
		// START META MOBILE
		if ($largura) $this->metaMobile($largura);
	}
	
	private function css($controller, $action)
	{
		//CSS DEFAULT
		$this->css->appendStylesheet('https://use.fontawesome.com/releases/v5.3.1/css/all.css');
		
		if (file_exists('/assets/css/'.$this->minifica.'normalize.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'normalize.css');
		
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/animate.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'animate.css');
		 
		//CSS DESKTOP
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/main.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'main.css' . $this->version, '');
		
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/input.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'input.css' . $this->version, '');
			
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/outbox.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'outbox.css' . $this->version, '');
		
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/' . strtolower($controller) . '.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'' . strtolower($controller) . '.css' . $this->version, '');
		
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/' . strtolower($controller) . '-'. strtolower($action) .'.css'))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'' . strtolower($controller) . '-'. strtolower($action) .'.css' . $this->version, '');

		//CSS RESPONSIVE
	    if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/main-responsive.css'))
	    	$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.'main-responsive.css' . $this->version, '');
	    
	    if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/responsive/main-responsive.css'))
	    	$this->css->appendStylesheet('/assets/'.$this->pasta.'css/responsive/'.$this->minifica.'main-responsive.css' . $this->version, '');
		    
    	if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/responsive/' . strtolower($controller) . '-responsive.css'))
    		$this->css->appendStylesheet('/assets/'.$this->pasta.'css/responsive/'.$this->minifica.'' . strtolower($controller) . '-responsive.css' . $this->version, '');
    	
    	if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/responsive/' . strtolower($controller) . '-'. strtolower($action) .'-responsive.css'))
    		$this->css->appendStylesheet('/assets/'.$this->pasta.'css/responsive/'.$this->minifica.'' . strtolower($controller) . '-'. strtolower($action) .'-responsive.css' . $this->version, '');
	    	
		//CSS minify
		$this->css_array = array_column($this->css->getContainer()->getArrayCopy(), 'href');
		$this->css_replace = $this->config_host=='local' ? true : false;
		$minify = new \Tropaframework\Css\Minify();
		$minify->generate($this->css_array, $this->css_replace);
	}
	
	private function js($controller, $action)
	{
		//JS VIEW
		$this->js->appendFile('https://code.jquery.com/jquery-1.12.4.min.js', 'text/javascript');
		
		$filename = '/assets/'.$this->pasta.'js/main.js';
		if ( \Tropaframework\Css\Minify::checkExists($filename) )
			$this->js->appendFile($filename . $this->version, 'text/javascript');
		
		$filename = '/assets/'.$this->pasta.'js/input.js';
		if ( \Tropaframework\Css\Minify::checkExists($filename) )
			$this->js->appendFile($filename . $this->version, 'text/javascript');
		
		$filename = '/assets/'.$this->pasta.'js/jquery.maskedinput.min.js';
		if ( \Tropaframework\Css\Minify::checkExists($filename) )
			$this->js->appendFile($filename, 'text/javascript');
		
		$filename = '/assets/'.$this->pasta.'js/jquery.maskMoney.min.js';
		if ( \Tropaframework\Css\Minify::checkExists($filename) )
			$this->js->appendFile($filename, 'text/javascript');
		
		$filename = '/assets/'.$this->pasta.'js/form.js';
		if ( \Tropaframework\Css\Minify::checkExists($filename) )
			$this->js->appendFile($filename . $this->version, 'text/javascript');
	}
	
	private function metaCharset($charset)
	{
		$this->meta->setCharset($charset);
	}
	
	private function metaMobile($largura)
	{
		$this->meta->appendHttpEquiv('X-UA-Compatible', 'IE=edge');
		$this->meta->appendName('viewport', 'width='.$largura.'px, user-scalable=no');
		$this->meta->appendName('apple-mobile-web-app-capable', 'yes');
		$this->meta->appendName('apple-mobile-web-app-status-bar-style', 'black-translucent');
		$this->meta->appendName('mobile-web-app-capable', 'yes');
		$this->meta->appendName('mobile-web-app-status-bar-style', 'black-translucent');
	}
	
	public function setMeta($name, $value)
	{
		$this->meta->appendName($name, $value);
	}

	public function setTitle($titulo)
	{
		$this->title->append($titulo);
	}
	
	public function setKeywords($array)
	{
		$this->meta->appendName('keywords', implode(', ', $array));
	}
	
	public function setDescription($text)
	{
		$this->meta->appendName('description', $text);
	}
	
	public function setCss($cssName)
	{
		if (\Tropaframework\Css\Minify::checkExists('/assets/'.$this->pasta.'css/'.$cssName))
			$this->css->appendStylesheet('/assets/'.$this->pasta.'css/'.$this->minifica.''.$cssName, '');
		
		//CSS minify
		$this->css_array = array_column($this->css->getContainer()->getArrayCopy(), 'href');
		$this->css_replace = $this->config_host=='local' ? true : false;
		$minify = new \Tropaframework\Css\Minify();
		$minify->generate($this->css_array, $this->css_replace);
	}
	
	public function setJs($jsName, $external = false)
	{
		if( $external )
		{
			$this->js->appendFile($jsName, 'text/javascript');
			
		} else {
			$filename = '/assets/'.$this->pasta.'js/'.$jsName;
			
			if( \Tropaframework\Css\Minify::checkExists($filename) )
				$this->js->appendFile($filename . $this->version, 'text/javascript');
		}
	}
	
	public function setScript($code, $type="text/javascript", $replace=array())
	{
		$this->js->appendScript($code, $type, $replace);
	}
	
	public function addGoogleAnalytics($id)
	{
		$this->setJs('https://www.googletagmanager.com/gtag/js?id=' . $id, true);
		
		$code = "window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '" . $id . "');";
		$this->setScript($code);
	}
	
	public function addGoogleTagManager($id)
	{
		//add google tag manager
		$code = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . $id . "');";
		$this->setScript($code);
		
		//add track case noscript
		//remenber add in layout "echo $this->placeholder('google');"
		$code = '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $id . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
		$viewHelperManager = $this->getServiceLocator->get('ViewHelperManager');
		$placeholder = $viewHelperManager->get('placeholder');
		$placeholder->getContainer('google')->set($code);
	}
	
	public function addGoogleTagManagerPush($params)
	{
		$code = "dataLayer.push(" . json_encode($params) . ");";
		$this->setScript($code);
	}
	
	public function addFacebookPixelCode($id)
	{
		//add facebook global code
		$code = "!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '" . $id . "');";
		$this->setScript($code);
		
		//add track PageView
		$this->addFacebookPixelCodeTrack('PageView');
		
		//add track case noscript
		//remenber add in layout "echo $this->placeholder('facebook');"
		$code = '<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=' . $id . '&ev=PageView&noscript=1"/></noscript>';
		$viewHelperManager = $this->getServiceLocator->get('ViewHelperManager');
		$placeholder = $viewHelperManager->get('placeholder');
		$placeholder->getContainer('facebook')->set($code);
	}
	
	public function addFacebookPixelCodeTrack($track, $params=array())
	{
		if( count($params) )
		{
			$code = "fbq('track', '" . $track . "', " . json_encode($params) . ");";
		} else {
			$code = "fbq('track', '" . $track . "');";
		}
		$this->setScript($code);
	}
	
	public function addCalendar()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.datepicker/jquery-ui.min.css');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.datepicker/jquery-ui.structure.min.css');
	
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.datepicker/jquery-ui.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.datepicker/init.js', 'text/javascript');
	}
	
	public function addCarousel()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.owl.carousel/owl.carousel.css');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.owl.carousel/owl.transitions.css');
		
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.owl.carousel/owl.carousel.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.owl.carousel/init.js', 'text/javascript');
	}
	
	public function addCountdown()
	{
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.countdown/jquery.countdown.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.countdown/init.js', 'text/javascript');
	}
	
	public function addFancybox()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.fancybox/jquery.fancybox.css');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.fancybox/fancybox.responsive.css');
		
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.fancybox/jquery.fancybox.pack.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.fancybox/init.js', 'text/javascript');
	}
	
	public function addHighcharts()
	{
	    $js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('https://code.highcharts.com/highcharts.src.js', 'text/javascript');
	}
	
	public function addScroll()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.perfect.scrollbar/css/perfect-scrollbar.min.css');
	
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.perfect.scrollbar/js/perfect-scrollbar.jquery.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.perfect.scrollbar/js/init.js', 'text/javascript');
	}
	
	public function addSelect2()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.select2/css/select2.min.css');
	
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.select2/js/select2.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.select2/js/init.js', 'text/javascript');
	}
	
	public function addSlider()
	{
		$css = $this->getServiceLocator->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet('/assets/application/js/extensions/jquery.nouislider/nouislider.min.css');
		
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.nouislider/nouislider.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.nouislider/init.js', 'text/javascript');
	}
	
	public function addValidation()
	{
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.validation/jquery.validate.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.validation/additional-methods.min.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.validation/localization/messages_pt_BR.js', 'text/javascript');
	}
	
	public function addMask()
	{
		$js = $this->getServiceLocator->get('ViewHelperManager')->get('headScript');
		$js->appendFile('/assets/application/js/extensions/jquery.mask/jquery.maskedinput.js', 'text/javascript');
		$js->appendFile('/assets/application/js/extensions/jquery.mask/jquery.maskMoney.min.js', 'text/javascript');
	}
}