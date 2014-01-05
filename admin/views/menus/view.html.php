<?php
/**
 * @package     Restaurant
 * @subpackage  com_restaurant
 *
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @copyright   Copyright (C) 2014 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * View class for a list of menus.
 *
 * @package     Restaurant
 * @subpackage  com_restaurant
 * @author      Bruno Batista <bruno@atomtech.com.br>
 * @since       3.2
 */
class RestaurantViewMenus extends JViewLegacy
{
	/**
	 * List of update items.
	 *
	 * @var     array
	 */
	protected $items;

	/**
	 * List pagination.
	 *
	 * @var     JPagination
	 */
	protected $pagination;

	/**
	 * The model state.
	 *
	 * @var     JObject
	 */
	protected $state;

	/**
	 * List of authors.
	 *
	 * @var     array
	 */
	protected $authors;

	/**
	 * The form filter.
	 *
	 * @var     JForm
	 */
	public $filterForm;

	/**
	 * List of active filters.
	 *
	 * @var     array
	 */
	public $activeFilters;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  Exception on failure, void on success.
	 *
	 * @since   3.2
	 */
	public function display($tpl = null)
	{
		try
		{
			// Initialise variables.
			$this->items         = $this->get('Items');
			$this->pagination    = $this->get('Pagination');
			$this->state         = $this->get('State');
			$this->authors       = $this->get('Authors');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);

			return false;
		}

		// Levels filter.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('J1'));
		$options[] = JHtml::_('select.option', '2', JText::_('J2'));
		$options[] = JHtml::_('select.option', '3', JText::_('J3'));
		$options[] = JHtml::_('select.option', '4', JText::_('J4'));
		$options[] = JHtml::_('select.option', '5', JText::_('J5'));
		$options[] = JHtml::_('select.option', '6', JText::_('J6'));
		$options[] = JHtml::_('select.option', '7', JText::_('J7'));
		$options[] = JHtml::_('select.option', '8', JText::_('J8'));
		$options[] = JHtml::_('select.option', '9', JText::_('J9'));
		$options[] = JHtml::_('select.option', '10', JText::_('J10'));

		$this->f_levels = $options;

		// We do not need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			// Load the submenu.
			RestaurantHelper::addSubmenu('menus');

			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$state = $this->get('State');
		$canDo = MenusHelper::getActions($state->get('filter.category_id'), 0, 'com_restaurant');
		$user  = JFactory::getUser();

		// Get the toolbar object instance.
		$bar   = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_RESTAURANT_MANAGER_MENUS_TITLE'), 'stack menus');

		if ($canDo->get('core.create') && count($user->getAuthorisedCategories('com_restaurant', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('menu.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('menu.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('menus.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('menus.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::custom('menus.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolbarHelper::archiveList('menus.archive');
			JToolbarHelper::checkin('menus.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'menus.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('menus.trash');
		}

		// Add a batch button.
		if ($user->authorise('core.create', 'com_restaurant') && $user->authorise('core.edit', 'com_restaurant') && $user->authorise('core.edit.state', 'com_restaurant'))
		{
			// Load the modal bootstrap script.
			JHtml::_('bootstrap.modal', 'collapseModal');

			// Instantiate a new JLayoutFile instance and render the batch button.
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = $layout->render(array('title' => $title));

			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($user->authorise('core.admin', 'com_restaurant'))
		{
			JToolbarHelper::preferences('com_restaurant');
		}

		JToolBarHelper::help('menus', $com = true);
	}
}
