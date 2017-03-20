<?php
namespace inowave\tableeditablewidget;

use yii\web\AssetBundle;
use yii\base\InvalidConfigException;

class TablewidgetAsset extends AssetBundle {
	//public $sourcePath = '@vendor/inowave/tableeditablewidget/assets';
	public $sourcePath = __DIR__ . '/assets';
	
    public $css = [
    	'css/x_editable/jqueryui-editable.css',        
        'css/tablewidget.css',        
    ];
	
    public $js = [
    	'js/x_editable/moment.min.js',
    	'js/x_editable/jqueryui-editable.min.js',    	
    	'js/x_editable/x-editable-radiolist.js',
    	'js/x_editable/x-editable-file-extension.js',
    	'js/tableWidget.js'
    ];

	public $depends = [
			'yii\web\YiiAsset'
		];
}