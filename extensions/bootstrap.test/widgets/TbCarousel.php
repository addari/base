<?php
/**
 * TbCarousel class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 */

Yii::import('bootstrap.widgets.TbListView');

/**
 * Bootstrap carousel widget.
 * http://twitter.github.com/bootstrap/javascript.html#carousel
 */
class TbCarousel extends TbListView
{
	/**
	 * Renders the data items for the view.
	 * Each item is corresponding to a single data model instance.
	 */
	public function renderItems()
	{
		$items = array();
		$data = $this->dataProvider->getData();

		if (!empty($data))
		{
			$owner = $this->getOwner();
			$render = $owner instanceof CController ? 'renderPartial' : 'render';
			foreach ($data as $i => $row)
			{
				$item = array();
				$d = $this->viewData;
				$d['index'] = $i;
				$d['data'] = $row;
				$d['widget'] = $this;
				$item['content'] = $owner->$render($this->itemView, $d, true);
				$items[] = $item;
			}
			echo TbHtml::carousel($items, $this->htmlOptions);
		}
		else
			$this->renderEmptyText();
	}
}