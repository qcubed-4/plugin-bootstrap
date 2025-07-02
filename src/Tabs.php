<?php

namespace QCubed\Bootstrap;

use QCubed\Project\Control\ControlBase;
use QCubed\Html;
use QCubed\QString;

class Tabs extends ControlBase
{
    protected string $strSelectedId;

    /**
     * Validates the current data or state.
     *
     * @return bool Returns true if the validation is successful, false otherwise.
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Parses the post data and processes it accordingly.
     *
     * @return mixed The result of the parsed post data.
     */
    public function parsePostData(): void
    {
    }

    /**
     * Generates the HTML structure for the control, including a set of tabs and corresponding tab panes.
     *
     * @return string The generated HTML for the control.
     */
    public function getControlHtml(): string
    {
        $strHtml = '';
        foreach ($this->objChildControlArray as $objChildControl) {
            $strInnerHtml = Html::renderTag('a',
                [
                    'href' => '#' . $objChildControl->ControlId . '_tab',
                    'aria-controls' => $objChildControl->ControlId . '_tab',
                    'role' => 'tab',
                    'data-toggle' => 'tab'
                ],
                QString::htmlEntities($objChildControl->Name)
            );
            $attributes = ['role' => 'presentation'];
            if ($objChildControl->ControlId == $this->strSelectedId) {
                $attributes['class'] = 'active';
            }

            $strTag = Html::renderTag('li', $attributes, $strInnerHtml);
            $strHtml .= $strTag;
        }
        $strHtml = Html::renderTag('ul', ['class' => 'nav nav-tabs', 'role' => 'tablist'], $strHtml);

        $strInnerHtml = '';
        foreach ($this->objChildControlArray as $objChildControl) {
            $class = 'tab-pane';
            $strItemHtml = null;
            if ($objChildControl->ControlId == $this->strSelectedId) {
                $class .= ' active';
            }
            $strItemHtml = $objChildControl->render(false);

            $strInnerHtml .= Html::renderTag('div',
                [
                    'role' => 'tabpanel',
                    'class' => $class,
                    'id' => $objChildControl->ControlId . '_tab'
                ],
                $strItemHtml
            );
        }
        $strTag = Html::renderTag('div', ['class' => 'tab-content'], $strInnerHtml);

        $strHtml .= $strTag;

        $strTag = $this->renderTag('div', null, null, $strHtml);

        return $strTag;
    }

    /**
     * Adds a child control to the current control and sets the first added control as the default selected control.
     *
     * @param ControlBase $objControl The child control to be added.
     *
     * @return void
     */
    public function addChildControl(ControlBase $objControl): void
    {
        parent::addChildControl($objControl);
        if (count($this->objChildControlArray) == 1) {
            $this->strSelectedId = $objControl->ControlId;    // default to first item added being selected
        }
    }
}
