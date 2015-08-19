<?php

namespace EllisLab\ExpressionEngine\Controller\Channels\Fields;

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Controller\Channels\AbstractChannels as AbstractChannelsController;
use EllisLab\ExpressionEngine\Model\Channel\ChannelField;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Channel\Fields\Fields Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Fields extends AbstractChannelsController {

	public function __construct()
	{
		parent::__construct();

		if ( ! ee()->cp->allowed_group(
			'can_access_admin',
			'can_admin_channels',
			'can_access_content_prefs'
		))
		{
			show_error(lang('unauthorized_access'));
		}

		$this->generateSidebar('field');

		ee()->lang->loadfile('admin');
		ee()->lang->loadfile('admin_content');
	}

	public function fields($group_id)
	{
		if (ee()->input->post('bulk_action') == 'remove')
		{
			$this->remove(ee()->input->post('selection'));
			ee()->functions->redirect(ee('CP/URL', 'channels/fields'));
		}

		$group = ee('Model')->get('ChannelFieldGroup')
			->filter('group_id', $group_id)
			->first();

		$base_url = ee('CP/URL', 'channels/fields');

		$vars = array(
			'create_url' => ee('CP/URL', 'channels/fields/create/' . $group->group_id)
		);

		$fields = ee('Model')->get('ChannelField')
			->filter('site_id', ee()->config->item('site_id'))
			->filter('group_id', $group_id);

		$table = $this->buildTableFromChannelFieldsQuery($fields);
		$table->setNoResultsText('no_fields', 'create_new', ee('CP/URL', 'channels/fields/create/' . $group_id));

		$vars['table'] = $table->viewData($base_url);

		$vars['pagination'] = ee('CP/Pagination', $vars['table']['total_rows'])
			->perPage($vars['table']['limit'])
			->currentPage($vars['table']['page'])
			->render($vars['table']['base_url']);

		ee()->javascript->set_global('lang.remove_confirm', lang('field') . ': <b>### ' . lang('fields') . '</b>');
		ee()->cp->add_js_script(array(
			'file' => array(
				'cp/confirm_remove',
			),
		));

		ee()->cp->set_breadcrumb(ee('CP/URL', 'channels/fields/groups'), lang('field_groups'));
		ee()->view->cp_page_title = sprintf(lang('custom_fields_for'), $group->group_name);

		ee()->cp->render('channels/fields/index', $vars);
	}

	public function create($group_id)
	{
		ee()->view->cp_breadcrumbs = array(
			ee('CP/URL', 'channels/fields/groups')->compile() => lang('field_groups'),
			ee('CP/URL', 'channels/fields/' . $group_id)->compile() => lang('fields'),
		);

		$errors = NULL;

		if ( ! empty($_POST))
		{
			$field = $this->setWithPost(
				ee('Model')->make('ChannelField', compact($group_id))
			);
			$result = $field->validate();

			if ($response = $this->ajaxValidation($result))
			{
			    return $response;
			}

			if ($result->isValid())
			{
				$field->save();

				ee()->session->set_flashdata('field_id', $field->field_id);

				ee('Alert')->makeInline('shared-form')
					->asSuccess()
					->withTitle(lang('create_field_success'))
					->addToBody(sprintf(lang('create_field_success_desc'), $field->field_label))
					->defer();

				ee()->functions->redirect(ee('CP/URL', 'channels/fields/'.$group_id));
			}
			else
			{
				$errors = $result;

				ee('Alert')->makeInline('shared-form')
					->asIssue()
					->withTitle(lang('create_field_error'))
					->addToBody(lang('create_field_error_desc'))
					->now();
			}
		}

		$vars = array(
			'errors' => $errors,
			'ajax_validate' => TRUE,
			'base_url' => ee('CP/URL', 'channels/fields/create/' . $group_id),
			'sections' => $this->form(),
			'save_btn_text' => sprintf(lang('btn_save'), lang('field')),
			'save_btn_text_working' => 'btn_saving',
			'form_hidden' => array(
				'field_id' => NULL,
			'group_id' => $group_id,
				'site_id' => ee()->config->item('site_id')
			),
		);

		ee()->view->cp_page_title = lang('create_field');

		ee()->cp->add_js_script('plugin', 'ee_url_title');

		ee()->javascript->output('
			$("input[name=field_label]").bind("keyup keydown", function() {
				$(this).ee_url_title("input[name=field_name]", true);
			});
		');

		ee()->cp->render('settings/form', $vars);
	}

	public function edit($id)
	{
		$field = ee('Model')->get('ChannelField', $id)
			->filter('site_id', ee()->config->item('site_id'))
			->first();

		if ( ! $field)
		{
			show_404();
		}

		ee()->view->cp_breadcrumbs = array(
			ee('CP/URL', 'channels/fields/groups')->compile() => lang('field_groups'),
			ee('CP/URL', 'channels/fields/' . $field->group_id)->compile() => lang('fields'),
		);

		$errors = NULL;

		if ( ! empty($_POST))
		{
			$field = $this->setWithPost($field);
			$result = $field->validate();

			if ($response = $this->ajaxValidation($result))
			{
			    return $response;
			}

			if ($result->isValid())
			{
				$field->save();

				ee('Alert')->makeInline('shared-form')
					->asSuccess()
					->withTitle(lang('edit_field_success'))
					->addToBody(sprintf(lang('edit_field_success_desc'), $field->field_label))
					->defer();

				ee()->functions->redirect(ee('CP/URL', 'channels/fields/edit/' . $id));
			}
			else
			{
				$errors = $result;

				ee('Alert')->makeInline('shared-form')
					->asIssue()
					->withTitle(lang('edit_field_error'))
					->addToBody(lang('edit_field_error_desc'))
					->now();
			}
		}

		$vars = array(
			'errors' => $errors,
			'ajax_validate' => TRUE,
			'base_url' => ee('CP/URL', 'channels/fields/edit/' . $id),
			'sections' => $this->form($field),
			'save_btn_text' => sprintf(lang('btn_save'), lang('field')),
			'save_btn_text_working' => 'btn_saving',
			'form_hidden' => array(
				'field_id' => $id,
				'group_id' => $field->group_id,
				'site_id' => ee()->config->item('site_id')
			),
		);

		ee()->view->cp_page_title = lang('edit_field');

		ee()->cp->render('settings/form', $vars);
	}

	private function setWithPost(ChannelField $field)
	{
		$field->site_id = ee()->config->item('site_id');
		$field->field_type = $_POST['field_type'];
		$field->group_id = ($field->group_id) ?: 0;
		$field->field_list_items = ($field->field_list_items) ?: '';
		$field->field_order = ($field->field_order) ?: 0;

		$field->set($_POST);
		return $field;
	}

	private function form(ChannelField $field = NULL)
	{
		if ( ! $field)
		{
			$field = ee('Model')->make('ChannelField');
		}

		$fieldtypes = ee('Model')->get('Fieldtype')
			->order('name')
			->all();

		$fieldtype_choices = array();

		foreach ($fieldtypes as $fieldtype)
		{
			$info = ee('App')->get($fieldtype->name);
			$fieldtype_choices[$fieldtype->name] = $info->getName();
		}

		$field->field_type = ($field->field_type) ?: 'text';

		$sections = array(
			array(
				array(
					'title' => 'type',
					'desc' => '',
					'fields' => array(
						'field_type' => array(
							'type' => 'select',
							'choices' => $fieldtype_choices,
							'group_toggle' => $fieldtypes->getDictionary('name', 'name'),
							'value' => $field->field_type
						)
					)
				),
				array(
					'title' => 'name',
					'desc' => 'name_desc',
					'fields' => array(
						'field_label' => array(
							'type' => 'text',
							'value' => $field->field_label,
							'required' => TRUE
						)
					)
				),
				array(
					'title' => 'short_name',
					'desc' => 'alphadash_desc',
					'fields' => array(
						'field_name' => array(
							'type' => 'text',
							'value' => $field->field_name,
							'required' => TRUE
						)
					)
				),
				array(
					'title' => 'instructions',
					'desc' => 'instructions_desc',
					'fields' => array(
						'field_instructions' => array(
							'type' => 'textarea',
							'value' => $field->field_instructions,
						)
					)
				),
				array(
					'title' => 'require_field',
					'desc' => 'require_field_desc',
					'fields' => array(
						'field_required' => array(
							'type' => 'yes_no',
							'value' => $field->field_required,
						)
					)
				),
				array(
					'title' => 'include_in_search',
					'desc' => 'include_in_search_desc',
					'fields' => array(
						'field_search' => array(
							'type' => 'yes_no',
							'value' => $field->field_search,
						)
					)
				),
				array(
					'title' => 'hide_field',
					'desc' => 'hide_field_desc',
					'fields' => array(
						'field_is_hidden' => array(
							'type' => 'yes_no',
							'value' => $field->field_is_hidden,
						)
					)
				),
			),
		);

		$field_options = $field->getSettingsForm();
		if (is_array($field_options) && ! empty($field_options))
		{
			$sections = array_merge($sections, $field_options);
		}

		foreach ($fieldtypes as $fieldtype)
		{
			if ($fieldtype->name == $field->field_type)
			{
				continue;
			}

			$dummy_field = ee('Model')->make('ChannelField');
			$dummy_field->field_type = $fieldtype->name;
			$field_options = $dummy_field->getSettingsForm();

			if (is_array($field_options) && ! empty($field_options))
			{
				$sections = array_merge($sections, $field_options);
			}
		}

		ee()->cp->add_js_script(array(
			'file' => array('cp/form_group'),
		));

		return $sections;
	}

	private function remove($field_ids)
	{
		if ( ! is_array($field_ids))
		{
			$field_ids = array($field_ids);
		}

		$fields = ee('Model')->get('ChannelField', $field_ids)
			->filter('site_id', ee()->config->item('site_id'))
			->all();

		$field_names = $fields->pluck('field_label');

		$fields->delete();
		ee('Alert')->makeInline('fields')
			->asSuccess()
			->withTitle(lang('success'))
			->addToBody(lang('fields_removed_desc'))
			->addToBody($field_names)
			->defer();
	}

}
