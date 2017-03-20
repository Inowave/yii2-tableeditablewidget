<?php
namespace inowave\tableeditablewidget;

use yii\web\AssetBundle;
use yii\base\InvalidConfigException;

class TablewidgetscriptsAsset extends AssetBundle {
	
	//public $sourcePath = '@vendor/inowave/tableeditablewidget/lib';
	public $sourcePath = __DIR__ . '/lib';
	
    public $css = [
        'css/jquery-ui/jquery-ui.min.css',
        'css/jquery-ui/jquery-ui.structure.min.css',
        'css/jquery-ui/cupertino/jquery-ui.theme.css',
    ];
	
    public $js = [
    	'js/jquery-ui/jquery-ui-1.10.4.custom.min.js', 
    	'js/jquery-ui/i18n/datepicker-ru.js',   	
    ];
}