<?php
namespace Dragon;

class Dragon extends \Slim\Slim {
    
    /**
     *  Extends the base Slim constructor to add default Views, routes and options
     */
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
        
        $this->get(
            '/:pagename/edit',
            function($pagename){
                $this->editPage($pagename);
            }
        );
        
        $this->post(
            '/:pagename/save',
            function($pagename){
                $this->savePage($pagename, $this->request->post());
            }
        );
        
        $this->post(
            '/content/upload',
            function(){
                // TODO: Handle errors for (e.g.) incompatable files, wrong file size
                echo json_encode(['path' => $this->saveImage((object)$_FILES['file']) ]);
            }
        );
        
    }
    
    /**
     *  Renders the markdown content into a Moustache template.
     *
     *  If no content exists (in content/$pagename.md) then a 404 response is 
     *  returned.
     *
     *  If a template exists with the same name as the page then this template 
     *  will be used, otherwise the default one will.
     *
     *  @param  $pagename   The name of the page to be renedered (usually the 
     *                      request url)
     */
    public function renderPage($pagename){
        $filename = 'content/'.$pagename.'.md';
        $page = array();
        
        $page['url'] = $pagename;
        
        if(file_exists($filename)){
            $page['content'] = \Parsedown::instance()->text(file_get_contents($filename));
            $page['name'] = ucfirst(str_replace('-',' ',$pagename));
        }else{
            // TODO: if logged in, allow page to be created
            $page['content'] = \Parsedown::instance()->text(file_get_contents('content/404.md'));
            $page['name'] = '404: Page Not Found';
            return $this->render('default.html', $page, 404);
        }
        
        if(is_file($this->view()->getTemplatePathname($pagename.'.html'))){
            $this->render($pagename, $page);
        }else{
            $this->render('default', $page);
        }
    }
    
    /**
     *  Renders an edit dialouge for this page, based on the _edit.html template
     *
     *  @param  $pagename   The name of the page to be edited (usually the 
     *                      request url)
     */
    public function editPage($pagename){
        $filename = 'content/'.$pagename.'.md';
        $page = array();
        
        $page['url'] = $pagename;
        
        if(file_exists($filename)){
            $page['content'] = file_get_contents($filename);
            $page['name'] = ucfirst(str_replace('-',' ',$pagename));
        }else{ // page doesn't exist - let renderPage() handle the 404
            return $this->renderPage($pagename);
        }
        
        $this->render('_edit',$page);
        
    }
    
    /**
     *  Saves page content and forwards to renderPage()
     *
     *  @param  $pagename   The name of the page to be edited (usually the 
     *                      request url)
     *  @param  $data       array of data to save to the page (including content)
     */
    public function savePage($pagename, $data){
        $filename = 'content/'.$pagename.'.md';
        
        // save content to file
        $content = $data['content'];
        file_put_contents($filename, $content);
        
        // TODO: save meta data (author, date, etc)
        
        // TODO: content versioning
        
        $this->renderPage($pagename);
    }
    
    /**
     *  Saves posted image to content images folder
     *
     *  @param  $data       file data object
     *  @return             the public path of the saved file
     */
    public function saveImage($data){
        $fileinfo = (object)pathinfo($data->name);
        $filename = 'content/img/'.$fileinfo->filename.'-'.time().'.'.$fileinfo->extension;
        
        // move uploaded image to destination
        move_uploaded_file($data->tmp_name, $filename);
        
        // TODO: Resize image
        
        return '/'.$filename;
        
    }
    
}

