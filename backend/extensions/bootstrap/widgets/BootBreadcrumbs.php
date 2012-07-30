<?php
/**
 * BootCrumb class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 */

Yii::import('zii.widgets.CBreadcrumbs');

/**
 * Bootstrap breadcrumb widget.
 */
class BootBreadcrumbs extends CBreadcrumbs
{
	/**
	 * @var string the separator between links in the breadcrumbs (defaults to ' / ').
	 */
	public $separator = '/';

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		$classes = 'breadcrumb';
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' '.$classes;
		else
			$this->htmlOptions['class'] = $classes;
	}

	/**
	 * Renders the content of the widget.
	 */
	public function run()
	{
		$links = array();

		if (!isset($this->homeLink))
			$this->homeLink = array('label'=>Yii::t('bootstrap', 'Home'), 'url'=>Yii::app()->homeUrl);

		if ($this->homeLink !== false)
		{
			if (!is_array($this->homeLink) || !isset($this->homeLink['label']) || !isset($this->homeLink['url']))
				throw new CException(__CLASS__.': homeLink must be an array with "label" and "url".');

			$label = $this->homeLink['label'];
			$url = $this->homeLink['url'];
			$active = Yii::app()->request->requestUri === Yii::app()->homeUrl;
			$label = $this->encodeLabel ? CHtml::encode($label) : $label;
			$content = !$active ? CHtml::link($label, $url) : $label;
			$links[] = $this->renderItem($content, $active);
		}
		
		foreach ($this->links as $label => $url)
		{
			if (is_string($label) || is_array($url))
			{
				$content = CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);
				$links[] = $this->renderItem($content);
			}
			else
				$links[] = $this->renderItem($this->encodeLabel ? CHtml::encode($url) : $url, true);
		}

		echo CHtml::openTag('ul', $this->htmlOptions);
		echo implode('', $links);
		echo '</ul>';
	}

	/**
	 * Renders a single breadcrumb item.
	 * @param string $content the content.
	 * @param boolean $active whether the item is active.
	 * @return string the markup.
	 */
	protected function renderItem($content, $active = false)
	{
		$separator = !$active ? '<span class="divider">'.$this->separator.'</span>' : '';
		
		ob_start();
		echo CHtml::openTag('li', $active ? array('class'=>'active') : array());
		echo $content.$separator;
		echo '</li>';
		return ob_get_clean();
	}
}
