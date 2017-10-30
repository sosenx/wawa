<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

function et_get_menu_fields() {

	return array(
		array(
			'id' => 'disable_titles',
			'type' => 'checkbox',
			'title' => 'Disable navigation label',
			'width' => 'wide',
			'value' => 1,
			'levels' => array(0,1)
		),
		array(
			'id' => 'anchor',
			'type' => 'text',
			'title' => 'Anchor',
			'width' => 'wide'
		),
		array(
			'id' => 'design',
			'type' => 'select',
			'title' => 'Design',
			'width' => 'wide',
			'options' => array(
				'dropdown' => 'Dropdown',
				'full-width' => 'Mega menu',
				'full-width open-by-click' => 'Mega menu open by click',
			),
			'levels' => 0
		),
		array(
			'id' => 'column_width',
			'type' => 'text',
			'title' => 'Column width (for ex.: 30%)',
			'width' => 'wide',
			'input_type' => 'number',
			'attributes' => array(
				'min' => 1,
				'max' => 100
			),
			'levels' => array(1)
		),
		array(
			'id' => 'design2',
			'type' => 'select',
			'title' => 'Design',
			'width' => 'wide',
			'options' => array(
				'' => 'design',
				'image' => 'Image',
				//'image-no-borders' => 'Image without spacing',
				//'image-column' => 'Image column',
			),
			'levels' => array(1,2)
		),
		array(
			'id' => 'columns',
			'type' => 'select',
			'title' => 'Columns',
			'width' => 'wide',
			'options' => array(
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5,
				6 => 6,
			),
			'levels' => 0
		),
		array(
			'id' => 'icon',
			'type' => 'text',
			'title' => 'Icon name (from fonts Awesome)',
			'width' => 'wide',
			'levels' => array(0,1,2)
		),
		array(
			'id' => 'label',
			'type' => 'select',
			'title' => 'Label',
			'width' => 'wide',
			'options' => array(
				'' => 'label',
				'hot' => 'Hot',
				'sale' => 'Sale',
				'new' => 'New',
			)
		),
		array(
			'id' => 'background_repeat',
			'type' => 'select',
			'title' => 'Background Repeat',
			'width' => 'thin',
			'options' => array(
				'' => 'background-repeat',
				'no-repeat' => 'No Repeat',
				'repeat' => 'Repeat All',
				'repeat-x' => 'Repeat Horizontally',
				'repeat-y' => 'Repeat Vertically',
				'inherit' => 'Inherit',
			),
			'levels' => 0
		),
		array(
			'id' => 'background_position',
			'type' => 'select',
			'title' => 'Background Position',
			'width' => 'thin',
			'options' => array(
				'' => 'background-position',
				'left top' => 'Left Top',
				'left center' => 'Left Center',
				'left bottom' => 'Left Bottom',
				'center center' => 'Center Center',
				'center bottom' => 'Center Bottom',
				'right top' => 'Right Top',
				'right center' => 'Right Center',
				'right bottom' => 'Right Bottom',
			),
			'levels' => 0
		),
	);
}