<?php

nextendimport('nextend.plugin.plugin');

class plgNextendMenuthemeClean {
    
    var $_name = 'clean';
    
    function onNextendMenuThemeList(&$list){
        $list[$this->_name] = $this->getPath();
    }
    
    static function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'clean'.DIRECTORY_SEPARATOR;
    }
}

NextendPlugin::addPlugin('nextendmenutheme', 'plgNextendMenuthemeClean');
