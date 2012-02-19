<?php
require_once('template.class.php');

class Application {
    var $lang;
    var $pageTemplate;
    var $headTemplate;
    var $contentTemplate;

    /**
     * Constructor
     */
    function Application($SITE, $lang='')
    {
        $this->lang = $lang;
	$this->pageTemplate = & new Template('templates/page_' . $this->lang . '.php'); // this is the outer template
	$this->pageTemplate->set('SITE', $SITE);
    }

    /**
     * set
     */
    function set($name, $value)
    {
        $this->pageTemplate->set($name, $value);
    }

    /**
     * render
     */
    function render()
    {
        $this->pageTemplate->set('content', $this->contentTemplate->render());
        $this->pageTemplate->set('head', $this->headTemplate->render());
	return $this->pageTemplate->render();
    }
}
?>
