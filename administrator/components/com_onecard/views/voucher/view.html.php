<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class OnecardViewVoucher extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user  = JFactory::getUser();
        $isNew = ($this->item->id == 0);
		if (!$isNew) {
		$toolbar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('joomla.toolbar.popup');
		$dhtml = $layout->render(array('name' => 'test','doTask' => '', 'text' => JText::_('Upload code từ NCC'), 'class' => 'icon-upload'));
        $generate = $layout->render(array('name' => 'generate','doTask' => '', 'text' => JText::_('Tạo code'), 'class' => 'icon-plus'));
        $renew = $layout->render(array('name' => 'renew','doTask' => '', 'text' => JText::_('Gia hạn'), 'class' => 'icon-clock'));
        $buy = $layout->render(array('name' => 'buy', 'doTask' => '', 'text' => JText::_('Mua code VTC'), 'class' => 'icon-cart'));
		$toolbar->appendButton('Custom', $dhtml);
        $toolbar->appendButton('Custom', $generate);
        $toolbar->appendButton('Custom', $renew);
        $toolbar->appendButton('Custom', $buy);
		}
        if (isset($this->item->checked_out))
        {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }
        else
        {
            $checkedOut = false;
        }

        $canDo = OnecardHelpersOnecard::getActions();
		
        JToolBarHelper::title(JText::_('COM_ONECARD_TITLE_VOUCHER'), 'voucher.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
        {
            JToolBarHelper::apply('voucher.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('voucher.save', 'JTOOLBAR_SAVE');
        }

        if (!$checkedOut && ($canDo->get('core.create')))
        {
            JToolBarHelper::custom('voucher.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create'))
        {
            JToolBarHelper::custom('voucher.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        // Button for version control
        if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit')) {
            JToolbarHelper::versions('com_onecard.voucher', $this->item->id);
        }

        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('voucher.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            JToolBarHelper::cancel('voucher.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
