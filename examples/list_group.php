<?php

require_once(__DIR__ . '/qcubed.inc.php');

use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Control\Label;
use QCubed\Action\Ajax;
use QCubed\QString;

class SampleForm extends QForm
{
    protected Bs\ListGroup $lg;
    protected Label $lblClicked;
    protected Bs\Pager $pager;

    protected function formCreate(): void
    {
        $this->lg = new Bs\ListGroup($this);
        $this->lg->setDataBinder("lg_Bind");
        $this->lg->setItemParamsCallback([$this, "lg_Params"]);
        $this->lg->addClickAction(new Ajax("lg_Action"));
        $this->lg->SaveState = true;

        $this->lblClicked = new Label($this);
        $this->lblClicked->Name = "Clicked on: ";

        $this->pager = new Bs\Pager($this);
        $this->pager->ItemsPerPage = 5;
        $this->lg->Paginator = $this->pager;
    }

    protected function lg_Bind(): void
    {
        $this->lg->TotalItemCount = Person::countAll();
        $clauses[] = $this->lg->LimitClause;
        $this->lg->DataSource = Person::loadAll($clauses);
    }

    public function lg_Params(Person $objPerson): array
    {
        $a['id'] = 'lg_' . $objPerson->Id;
        $a['html'] = QString::htmlEntities($objPerson->FirstName . ' ' . $objPerson->LastName);
        return $a;
    }

    /**
     * Handles an action triggered by a form or control, updates the label with a person's full name.
     *
     * @param string $strFormId The ID of the form that triggered the action.
     * @param string $strControlId The ID of the control that triggered the action.
     * @param string $strActionParam Additional parameters passed with the action.
     *
     * @return void
     */
    protected function lg_Action(string $strFormId, string $strControlId, string $strActionParam): void
    {
        $id = substr($strActionParam, 3);
        $objPerson = Person::load($id);
        $this->lblClicked->Text = $objPerson->FirstName . ' ' . $objPerson->LastName;
    }
}

SampleForm::run('SampleForm');
