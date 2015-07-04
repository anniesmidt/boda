<?php

nextendimport('nextend.plugin.plugin');

class plgNextendMenuthemeElegant {
    
    var $_name = 'elegant';
    
    function onNextendMenuThemeList(&$list){
        $list[$this->_name] = $this->getPath();
    }
    
    static function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'elegant'.DIRECTORY_SEPARATOR;
    }
}

NextendPlugin::addPlugin('nextendmenutheme', 'plgNextendMenuthemeElegant');
