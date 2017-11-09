<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Gift
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
/**
 * Content Component Category Tree
 *
 * @since  1.6
 */
class GiftCategories extends JCategories
{
    /**
     * Class constructor
     *
     * @param   array  $options  Array of options
     *
     * @since   11.1
     */
    public function __construct($options = array())
    {
        $options['table'] = '#__gift_item';
        $options['extension'] = 'com_gift';

        parent::__construct($options);
    }
}
