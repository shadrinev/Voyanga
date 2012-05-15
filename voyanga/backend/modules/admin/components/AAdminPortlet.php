<?php
Yii::import("zii.widgets.CPortlet");
/**
 * A base class for admin portlets
 * @author Charles Pick
 * @package packages.admin.components
 */
class AAdminPortlet extends CPortlet
{
    /**
     * The tag name for the container
     * @var string
     */
    public $tagName = "div";
    /**
     * @var array the HTML attributes for the portlet container tag.
     */
    public $htmlOptions = array('class' => 'row');

    /**
     * @var string the CSS class for the decoration container tag.
     */
    public $decorationCssClass = 'span2';
    /**
     * @var string the CSS class for the portlet title tag.
     */
    public $titleCssClass = '';
    /**
     * @var string the CSS class for the content container tag. Defaults to 'content'.
     */
    public $contentCssClass = 'content';
    /**
     * An array of CMenu items to show in the header.
     * If this array is not set no menu will be shown
     * @var array
     */
    public $menuItems;
    /**
     * The configuration for the header menu, if shown
     * @var array
     */
    public $menuConfig = array();
    /**
     * An array of CMenu items to show in the sidebar.
     * If this array is not set no sidebar will be shown
     * @var array
     */
    public $sidebarMenuItems;
    /**
     * The configuration for the sidebar menu, if shown
     * @var array
     */
    public $sidebarMenuConfig = array("type" => "list");
    /**
     * The htmlOptions for the sidebar, if shown
     * @var array
     */
    public $sidebarHtmlOptions = array("class" => "sidebar");
    /**
     * Extra content to show in the sidebar, this will not be html encoded!
     * @var string
     */
    public $sidebarContent;

    private $_openTag;

    /**
     * Initializes the widget.
     * This renders the open tags needed by the portlet.
     * It also renders the decoration, if any.
     */
    public function init()
    {
        ob_start();
        ob_implicit_flush(false);

        $this->htmlOptions['id'] = $this->getId();
        echo CHtml::openTag($this->tagName, $this->htmlOptions) . "\n";
        $this->renderDecoration();

        echo "<div class=\"span10\">\n";
        echo "<h1>{$this->title}</h1>";

        $this->_openTag = ob_get_contents();
        ob_clean();
    }

    /**
     * Renders the content of the portlet.
     */
    public function run()
    {
        $this->renderContent();
        $content = ob_get_clean();
        if ($this->hideOnEmpty && trim($content) === '')
            return;
        echo $this->_openTag;
        echo $content;
        echo "</div>\n";
        echo CHtml::closeTag($this->tagName);
    }

    /**
     * Renders the decoration for the portlet.
     * The default implementation will render the title if it is set.
     */
    protected function renderDecoration()
    {
        if ((!is_array($this->sidebarMenuItems)) && (sizeof($this->menuItems)==0))
        {
            echo "<div class=\"{$this->decorationCssClass}\">&nbsp;\n</div>\n";
            return;
        }
        echo "<div class=\"{$this->decorationCssClass}\">\n<div class=\"well\">\n";
        if (!is_array($this->sidebarMenuItems))
            $this->sidebarMenuItems = array();
        $menuConfig = $this->sidebarMenuConfig;
        $menuConfig['items'] = $this->sidebarMenuItems;
        if (sizeof($this->menuItems)>0)
            $menuConfig['items'] = CMap::mergeArray($menuConfig['items'], array(array('label'=>'Действия')), $this->menuItems);
        $this->widget("bootstrap.widgets.BootMenu", $menuConfig);
        echo "</div>\n</div>\n";
    }
}