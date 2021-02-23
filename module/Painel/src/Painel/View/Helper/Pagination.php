<?php
namespace Painel\View\Helper;
use Zend\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{
	protected $nav;

	public function __invoke($nav)
	{
		$this->nav = $nav;
		return $this->renderHtml();
	}

	public function renderHtml()
	{
		$html = '';

		if( is_array($this->nav['nav']) )
		{
			$html .= '<div class="paginator">';
			
			//first and prev
			if( $this->nav['current'] == $this->nav['first'] )
			{
				$html .= '<span class="prev disabled"><i class="fa fa-angle-double-left"></i></span>';
				$html .= '<span class="prev disabled"><i class="fa fa-angle-left"></i></span>';
			} else {
				$html .= '<a class="prev link" href="' . $this->getLink(1) . '"><i class="fa fa-angle-double-left"></i></a>';
				$html .= '<a class="prev link" href="' . $this->getLink($this->nav['prev']) . '"><i class="fa fa-angle-left"></i></a>';
			}
			
			//navigation
			foreach( $this->nav['nav'] as $page )
			{
				if( $this->nav['current'] == $page )
				{
					$html .= '<span class="page disabled">' . $page . '</span>';
				} else {
					$html .= '<a class="page link" href="' . $this->getLink($page) . '">' . $page . '</a>';
				}
			}
			
			//last and next
			if( $this->nav['current'] == $this->nav['last'] )
			{
				$html .= '<span class="next disabled"><i class="fa fa-angle-right"></i></span>';
				$html .= '<span class="next disabled"><i class="fa fa-angle-double-right"></i></span>';
			} else {
				$html .= '<a class="next link" href="' . $this->getLink($this->nav['next']) . '"><i class="fa fa-angle-right"></i></a>';
				$html .= '<a class="next link" href="' . $this->getLink($this->nav['last']) . '"><i class="fa fa-angle-double-right"></i></a>';
			}
			
			$html .= '</div>';
		}

		return $html;
	}
	
	public function getLink($page)
	{
		if( sizeof($_GET) > 0 )
		{
			$g = \Application\Classes\Security::antiInjection($_GET);
			$q = array();
			unset($g['page']);
			while( list($k,$v) = each($g) ){
				$q[] = $k .'='.$v;
			}
			$q = '&' . implode('&',$q);
			$q = $q!='&' ? $q : null;
		}
		
		$link = strtok($_SERVER["REQUEST_URI"], '?') . '?page=' . $page . $q;
		return $link;
	}
}
?>