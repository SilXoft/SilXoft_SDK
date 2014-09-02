<?php
namespace Sl\Module\Home\Modulerelation\Table;
class Citycountry extends \Sl\Modulerelation\DbTable {

	protected $_name = 'cities_country';
	protected $_primary = 'id';
	protected $_referenceMap = array(
		'Sl\Module\Home\Model\City' => array(
			'columns' => 'city_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\City',
			'refColums' => 'id'
		),
		'Sl\Module\Home\Model\Country' => array(
			'columns' => 'country_id',
			'refTableClass' => 'Sl\Module\Home\Model\Table\Country',
			'refColums' => 'id'
		),
	);
	
	
}
