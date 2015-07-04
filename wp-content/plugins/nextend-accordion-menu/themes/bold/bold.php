<?php

nextendimport('nextend.plugin.plugin');

class plgNextendMenuthemeBold {
    
    var $_name = 'bold';
    
    function onNextendMenuThemeList(&$list){
        $list[$this->_name] = $this->getPath();
    }
    
    static function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'bold'.DIRECTORY_SEPARATOR;
    }
}

NextendPlugin::addPlugin('nextendmenutheme', 'plgNextendMenuthemeBold');
