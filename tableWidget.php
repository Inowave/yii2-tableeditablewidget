<?php
namespace inowave\tableeditablewidget;

use inowave\tableeditablewidget\TablewidgetAsset; //asset with table widget css
use inowave\tableeditablewidget\TablewidgetscriptsAsset; 

use \Yii;
use yii\helpers\Html;
use yii\web\View;
use yii\base\Widget;
use yii\helpers\Inflector;
use yii\base\InvalidConfigException;

class tableWidget extends Widget {
	public $data;
	private $type_script;
	
	public function init() {
		parent::init();
		$this->view = $this->getView();
		$this->registerAssetsStart();
		
		$data = $this->data;
		if(!empty($data)) {
			
			// wrap container the same width as table
			$wrap_div_options = ['class'=>'tablewidget_wrap'];
			if(isset($data['options']['width'])) {
				Html::addCssStyle($wrap_div_options, 'width: '.$data['options']['width']);
			} elseif(isset($data['options']['style']['width'])) {
				Html::addCssStyle($wrap_div_options, 'width: '.$data['options']['style']['width']);
			}
			
			echo Html::beginTag('div',$wrap_div_options);
			
			$headers = $body = '';
			if(isset($data['headers']['columns']) && !empty($data['headers']['columns'])) {
				$headers = $data['headers']['columns'];
				
				// columns_list
				echo $this->getColumnsList($headers);
				
			}
			if(isset($data['body']['columns']) && !empty($data['body']['columns'])) {
				$body = $data['body']['columns'];
			}
			
			// div wrap options 
			$wrap_options = [];
			if(isset($data['options'])) {
				$wrap_options = $data['options'];
			}
			Html::addCssClass($wrap_options, 'tablewidget');			
			echo Html::beginTag('div',$wrap_options);
			
			// headers
			if(count($headers)) {
				
				// header options 
				$headers_options = [];
				if(isset($data['headers']['options'])) {
					$headers_options = $data['headers']['options'];
				}
				Html::addCssClass($headers_options, 'headers_table');				
				echo Html::beginTag('table',$headers_options);
				
				echo Html::beginTag('thead');
				echo Html::beginTag('tr');
				foreach ($headers as $h_index => $h_row) {
					
					$headcell_options = [];
					if(isset($h_row['options'])) {
						$headcell_options = $h_row['options'];
					}
					
					if(isset($h_row['head_cell']['width'])) {
						Html::addCssStyle($headcell_options, 'width: '.$h_row['head_cell']['width']);
					}
					
					echo Html::tag('th', (isset($h_row['head_cell']['value']) ? $h_row['head_cell']['value'] : '' ), (!empty($headcell_options) ? $headcell_options : []));
					
					// register js and css
					if(isset($h_row['data']['data-type'])) {
						if($h_row['data']['data-type']=='select2') {
							$this->type_script['select2'] = 'select2';							
						} elseif($h_row['data']['data-type']=='time') {
							$this->type_script['time'] = 'time';
						}						
					}
				}
				echo Html::endTag('tr');
				echo Html::endTag('thead');
				echo Html::endTag('table');
			}
			
			
			// columns			
			if(count($body)) {
				echo Html::beginTag('div', ['class' => 'table_body_wrap']);	
				
				$body_options = [];
				if(isset($data['body']['options'])) {
					$body_options = $data['body']['options'];
				}
				
				Html::addCssClass($body_options, 'body_table');
				
				echo Html::beginTag('table', $body_options);	
				echo Html::beginTag('tbody');	
							
				foreach ($body as $b_index => $b_row) {
		
					echo Html::beginTag('tr', (isset($b_row['options']) ? $b_row['options'] : []));
					
					if(isset($b_row['row_cell']) && !empty($b_row['row_cell'])) {
						foreach($b_row['row_cell'] as $cell_index => $cell) {
							
							$td_options = [];
							if(isset($cell['options'])) {
								$td_options = $cell['options'];
							}
							if(isset($headers[$cell_index]['head_cell']['width'])) {
								Html::addCssStyle($td_options, 'width: '.$headers[$cell_index]['head_cell']['width']);
							}
							if(isset($headers[$cell_index]['options']['class'])) {
								Html::addCssClass($td_options, $headers[$cell_index]['options']['class']);
							}
							if(isset($headers[$cell_index]['head_cell']['name'])) {
								$td_options['name'] = $headers[$cell_index]['head_cell']['name'];
								$td_options['data-strnum'] = $b_row['options']['id'];//$b_index;
							}
							if(isset($headers[$cell_index]['data']) && !empty($headers[$cell_index]['data'])) {
								$td_options = array_merge($td_options, $headers[$cell_index]['data']);
							}
							
							echo Html::beginTag('td', (!empty($td_options) ? $td_options : []));
								echo Html::tag('span', (isset($cell['value']) ? $cell['value'] : '' ), ['class'=>'editable_span'] );
							echo Html::endTag('td');
						}
					}
					echo Html::endTag('tr');
				}				
				echo Html::endTag('tbody');
				echo Html::endTag('table');
				echo Html::endTag('div');
			}
			echo Html::endTag('div');
			echo Html::endTag('div');
		}
	
		$this->registerFieldScripts();
		$this->registerAssetsEnd();
	}
	public function run() {
		
	}

	private function getColumnsList($headers) {
		// headers
		if(count($headers)) {
				
			echo Html::beginTag('div', ['class'=>'table_collist']);
			echo Html::tag('div', 'Columns list', ['class'=>'collist_title']);
			
			echo Html::beginTag('div', ['class'=>'collist_list']);

			$selection = $items = [];
			foreach ($headers as $h_index => $h_row) {
					if(isset($h_row['options']['class'])) {
						$selection[] = $h_row['options']['class'];
						$items[$h_row['options']['class']] = (isset($h_row['head_cell']['value']) ? $h_row['head_cell']['value'] : '' );
					}
			}
			echo Html::checkboxList('column_check', $selection, $items, ['tag' => false]);
			
			echo Html::endTag('div');
			echo Html::endTag('div');
		}
	}

	/**
	 * Registers the needed assets
	 */
	public function registerAssetsStart() {
		$view = $this->getView();
		TablewidgetscriptsAsset::register($view);
	}
	public function registerAssetsEnd() {
		$view = $this->getView();
		TablewidgetAsset::register($view);
	}
	
	private function registerFieldScripts() {
		if(isset($this->type_script) && !empty($this->type_script)) {
			if(isset($this->type_script['time'])) {
				echo \inowave\inputmaskwidget\inputmask::widget();
				echo \inowave\timepickerwidget\timepicker::widget();
			} 
		
			if(isset($this->type_script['select2'])) {
				echo \inowave\select2widget\select2::widget();
			}
		}
	}
}

 /*
$table_array = [
 			'options' => ['style' => ['width' => '100%', 'height' => '250px']], // options of wrap div 
 			'headers' => [
 				'options' => ['style' => ['border-bottom-width' => '3px']], // options of headers table
 				'columns' => [
 					[
 						'options' => ['style' => ['color' => '#666'], 'class' => 'column_1'],
 						'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля', 'data-mode' => 'inline','data-inputclass' => "editable_text_field"],
 						'head_cell' => [ 'name' => 'field1', 'value' => 'текстовое поле', 'width' => '100px'],
 					],
 					
					[
						'options' => ['class' => 'column_2'],
						//'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля'],
						'data' => ['data-type' => 'date', 'data-title' => 'Поле дата', 'data-viewformat' => "dd.mm.yyyy" , 'data-mode' => 'inline' , 'data-datepicker'=> '{"showOn":"focus", "firstDay":"1"}'],
 						'head_cell' => [ 'name' => 'field2', 'value' => 'Поле дата',  'width' => '10%'],
 					],
 					
					[
						'options' => [ 'class' => 'column_3'],
						'data' => ['data-type' => 'textarea', 'data-title' => 'Введите название поля', 'data-mode' => 'inline'],
 						'head_cell' => [ 'name' => 'field3', 'value' => 'поле textarea'],
 					],
 					
					[
						'options' => ['class' => 'column_4'],
						'data' => ['data-type' => 'select', 'data-title' => 'Введите название поля', 'data-mode' => 'inline'],
 						'head_cell' => [ 'name' => 'field4', 'value' => 'поле select', 'width' => '200px'],
 						// data-value="5" data-source="/groups"
 					],
 					
					[
						'options' => ['class' => 'column_5'],
						'data' => ['data-type' => 'checklist', 'data-title' => 'Введите название поля', 'data-mode' => 'inline'],
 						'head_cell' => [ 'name' => 'field5', 'value' => 'поле checklist'],
 					],
 					[
						'options' => ['class' => 'column_6'],
						'data' => ['data-type' => 'radiolist', 'data-title' => 'Введите название поля', 'data-mode' => 'inline','data-showbuttons' => 'false','data-instring' => 'true'],
 						'head_cell' => [ 'name' => 'field6', 'value' => 'поле radiolist'],
 					],
 					[
						'options' => ['class' => 'column_7'],
						'data' => ['data-type' => 'select2', 'data-title' => 'Введите название поля', 'data-mode' => 'inline'],
 						'head_cell' => [ 'name' => 'field7', 'value' => 'поле select2 (multicomplete)',  'width' => '150px'],
 					],
 					[
						'options' => ['class' => 'column_8'],
						//'data' => ['data-type' => 'time', 'data-title' => 'Введите название поля', 'data-format' => "HH:mm", 'data-inputclass' => "editable_time_field", 'data-mode' => 'inline'],
						'data' => ['data-type' => 'time', 'data-title' => 'Введите название поля', 'data-format' => "HH:mm", 'data-mode' => 'inline'],
 						'head_cell' => [ 'name' => 'field8', 'value' => 'поле time'],
 					],
					[
 						'options' => ['class' => 'column_9'],
 						'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_int_field",
									'data-numbermin' => "-10",'data-numbermax' => "100", 'data-numberseparator' => ' '],
 						'head_cell' => [ 'name' => 'field9', 'value' => 'поле int'],
 					],
					[
 						'options' => ['class' => 'column_10'],
 						'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_real_field",
									'data-numbermin' => "-50.45",'data-numbermax' => "100.77", 'data-numberseparator' => ' ','data-numberradix' => ',', 'data-numberdigits' => 2],
 						'head_cell' => [ 'name' => 'field10', 'value' => 'поле real'],
 					],
					[
 						'options' => ['class' => 'column_11'],
 						'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_percent_field",
									'data-numberlen' => "3",'data-numberneg' => "false", 'data-numbersuffix' => ' %', 'data-numberdigits' => 2],
 						'head_cell' => [ 'name' => 'field11', 'value' => 'поле %'],
 					],
					[
 						'options' => ['class' => 'column_12'],
 						'data' => ['data-type' => 'text', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_price_field",
								'data-numbermin' => "-10",'data-numbermax' => "100", 'data-numbersuffix' => ' руб.', 'data-numberprefix' => '== ', 'data-numberdigits' => 2],
 						'head_cell' => [ 'name' => 'field12', 'value' => 'поле цена'],
 					],
 					[
 						'options' => ['class' => 'column_13'],
 						'data' => ['data-type' => 'file', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_imageupload_field", 'data-filetypes'=>'png,jpg'],
 						'head_cell' => [ 'name' => 'field13', 'value' => 'поле загрузки картинки',  'width' => '150px'],
 					],
 					[
 						'options' => ['class' => 'column_14'],
 						'data' => ['data-type' => 'file', 'data-title' => 'Введите название поля', 'data-mode' => 'inline', 'data-inputclass' => "editable_fileupload_field", 'data-filetypes'=>'xls,xlsx,txt,rar'],
 						'head_cell' => [ 'name' => 'field14', 'value' => 'поле загрузки файла',  'width' => '150px'],
 					],
				]
			],
			'body' => [
				'options' => ['style' => ['border-bottom-width' => '3px']], // options of body table
				'columns' => [
					// row data
					[
						// row options
						// id - id of record in database
						'options' => ['id' => 1 ,'class' => 'row-class', 'style' => ['background-color' => '#ff4455']],
						'row_cell' => [
							// cell data
							[
								'options' => ['class' => 'cell-class', 'style' => ['background-color' => '#eee']],
								'value' => 'ячейка 1',
							],
							[
								'options' => ['class' => 'cell-class', 'style' => ['background-color' => '#eee']],
								'value' => '22.01.2017',
							],
							[
								'value' => 'ячейка 3',
							],
							[
								'value' => 'ячейка 4',
							],	
							[
								'value' => 'ячейка 5',
							],
							[
								'value' => 'ячейка 6',
							],
							[
								'value' => 'ячейка 7',
							],
							[
								'value' => '00:21',
							],

							[
								'value' => '45',
							],
							[
								'value' => '-45,56',
							],
							[
								'value' => '45.56 %',
							],
							[
								'value' => '== 45.56 руб.',
							],
							[
								'value' => '1',
							],
							[
								'value' => '1',
							],
						]
					],
					
					// row data
					[
						'options' => ['id' => 2],
						'row_cell' => [
							// cell data
							[
								'options' => ['style' => ['background-color' => '#ff4455']],
								'value' => 'ячейка_11_ячейка_11_ячейка_11_',
							],
							[
								'value' => '22.01.2017',
							],
							[
								'value' => 'Фьюжн колеблет космический октавер, это понятие создано по аналогии с термином Ю.Н.Холопова "многозначная тональность". Узел, по определению, меняет перигей. Эти слова совершенно справедливы, однако цикл неравномерен. В связи с этим нужно подчеркнуть, что поп-индустрия многопланово использует далекий фьюжн. Комета оценивает метеорит, это понятие создано по аналогии с термином Ю.Н.Холопова "многозначная тональность".',
							],
							[
								'value' => 'ячейка 14',
							],		
							[
								'value' => 'ячейка 5',
							],
							[
								'value' => 'ячейка 6',
							],		
							[
								'value' => 'ячейка 7',
							],	
							[
								'value' => '05:22',
							],		

							[
								'value' => '45',
							],
							[
								'value' => '-45,56',
							],
							[
								'value' => '45.56 %',
							],
							[
								'value' => '== 45.56 руб.',
							],
							[
								'value' => '1',
							],
							[
								'value' => '1',
							],
						]
					],
				]
			]
 		];   */
