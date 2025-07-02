<?php

require_once(__DIR__ . '/qcubed.inc.php');

use QCubed\Bootstrap as Bs;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Event\Click;
use QCubed\Action\Ajax;
use QCubed\Event\DialogButton;
use QCubed\Action\ActionParams;

class SampleForm extends QForm
{
    /** @var  Bs\Modal */
    protected Bs\Modal $modal1;
    /** @var Bs\Button */
    protected Bs\Button $btn1;

    protected Bs\Modal $modal2;
    protected Bs\Button $btn2;

    protected function formCreate(): void
    {
        $this->btn1 = new Bs\Button($this);
        $this->btn1->addAction(new Click(), new Ajax('showDialog'));
        $this->btn1->ActionParameter = 1;
        $this->btn1->Text = "Show Modal 1";

        $this->modal1 = new Bs\Modal($this);
        $this->modal1->Text = "Hi there";
        $this->modal1->Title = "Simple Modal";

        $this->btn2 = new Bs\Button($this);
        $this->btn2->addAction(new Click(), new Ajax('showDialog'));
        $this->btn2->ActionParameter = 2;
        $this->btn2->Text = "Show Modal 2";

        $this->modal2 = new Bs\Modal($this);
        $this->modal2->Text = "Hi there";
        $this->modal2->Title = "Modal with Buttons";
        $this->modal2->addButton('Watch Out', 'wo', false, false, "Are you sure?",
            ['class' => Bs\Bootstrap::BUTTON_WARNING]);
        $this->modal2->addCloseButton('Cancel');
        $this->modal2->addButton('OK', 'ok', false, true);
        $this->modal2->addAction(new DialogButton(), new Ajax('buttonClick2'));
    }


    /**
     * Displays a dialog box for the specified control.
     *
     * @param string $strFormId The ID of the form within which the dialog is triggered.
     * @param string $strControlId The ID of the control that initiated the action.
     * @param string $strActionParam Additional parameter used to determine the specific dialog to show.
     *
     * @return void
     */
    public function showDialog(string $strFormId, string $strControlId, string $strActionParam): void
    {
        $strControlName = 'modal' . $strActionParam;
        $this->$strControlName->showDialogBox();

    }

    /**
     * Handles the button click event, hides the modal dialog box,
     * and displays an alert with the button parameter.
     *
     * @param string $strFormId The ID of the form that contains the button.
     * @param string $strControlId The ID of the control that was clicked.
     * @param string $strParameter Additional parameter provided for the button click.
     *
     * @return void
     */
    public function buttonClick2(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->modal2->hideDialogBox();
        Bs\Modal::alert("Button '" . $strParameter . "' was clicked");
    }
}

SampleForm::run('SampleForm');
