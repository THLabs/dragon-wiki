<?php
namespace Dragon;

class Dragon extends \Slim\Slim {
    
    public function __construct() {
        
        parent::__construct(array(
            'view' => new \Slim\Mustache\Mustache(),
            'templates.path' => __DIR__.'\..\templates'
        ));
        
        // set template options
        $this->view()->parserOptions = array(
            'loader' => new \Mustache_Loader_FilesystemLoader($this->view()->getTemplatesDirectory(), array('extension' => '.html'))
        );
        
        $this->get(
            '/',
            function (){
                $this->renderPage('home');
            }
        );

        $this->get(
            '/:pagename',
            function($pagename){
                $this->renderPage($pagename);
            }
        );
        
    }
    
    protected function renderPage($pagename){
        $filename = 'content/'.$pagename.'.md';
        $page = array();
        
        if(file_exists($filename)){
            $page['content'] = \Parsedown::instance()->text(file_get_contents($filename));
            $page['name'] = ucfirst($pagename);
        }else{
            $page['content'] = \Parsedown::instance()->text(file_get_contents('content/404.md'));
            $page['name'] = '404: Page Not Found';
        }
        
        if(is_file($this->view()->getTemplatePathname($pagename.'.html'))){
            $this->render($pagename.'.html', $page);
        }else{
            $this->render('default.html', $page);
        }
    }
    
}

