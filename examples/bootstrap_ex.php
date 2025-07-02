<?php
require_once(__DIR__ . '/qcubed.inc.php');

use QCubed\Bootstrap as Bs;
use QCubed\Control\RadioButtonList;
use QCubed\Html;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\QString;
use QCubed\Query\QQ;

class SampleForm extends QForm
{
    protected Bs\Navbar $navBar;
    protected Bs\Carousel$carousel;

    protected Bs\Accordion $accordion;

    protected Bs\RadioList $lstRadio1;
    protected Bs\RadioList$lstRadio2;

    protected Bs\Dropdown $lstPlain;

    /**
     * Initializes and configures various components of the form.
     *
     * The method orchestrates the creation of multiple key UI components,
     * including a navigation bar, carousel, accordion, radio list, and dropdown menus.
     * These components are set up to enhance the user interface of the application.
     *
     * @return void
     */
    protected function formCreate(): void
    {
        $this->navBar_Create();
        $this->carousel_Create();
        $this->accordion_Create();
        $this->radioList_Create();
        $this->dropdowns_Create();
    }

    /**
     * Initializes and configures the navigation bar for the application.
     *
     * The method creates a bootstrap-based navigation bar with a logo header, styled with an inverse theme.
     * It dynamically populates dropdown menus with links to list and edit forms, scanning files in a specific directory.
     *
     * @return void
     */
    protected function navBar_Create(): void
    {
        $this->navBar = new Bs\Navbar($this, 'navbar');

        //$this->objMenu->addCssClass('navbar-ryaa');
        $url = QCUBED_APP_TOOLS_URL . '/start_page.php';
        $this->navBar->HeaderText = Html::renderTag("img",
            ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed-4_logo_footer.png", "alt" => "Logo"], null, true);
        $this->navBar->HeaderAnchor = $url;
        $this->navBar->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;

        $objList = new Bs\NavbarList($this->navBar);
        $objListMenu = new Bs\NavbarDropdown('List');
        $objEditMenu = new Bs\NavbarDropdown('New');

        // Add all the lists and edits in the drafts directory
        $list = scandir(QCUBED_FORMS_DIR);
        foreach ($list as $name) {
            if ($offset = strpos($name, '_list.php')) {
                $objListMenu->addItem(new Bs\NavbarItem(substr($name, 0, $offset), null,
                    QCUBED_FORMS_URL . '/' . $name));
            } elseif ($offset = strpos($name, '_edit.php')) {
                $objEditMenu->addItem(new Bs\NavbarItem(substr($name, 0, $offset), null,
                    QCUBED_FORMS_URL . '/' . $name));
            }
        }

        $objList->addMenuItem($objListMenu);;
        $objList->addMenuItem($objEditMenu);

        /*

        $objRandomMenu = new Bs\NavbarDropdown('Contribute');

        $objList->addMenuItem(new Bs\NavbarItem("Login", __SUBDIRECTORY__ . '/private/login.html', 'navbarLogin'));
        */

    }

    /**
     * Initializes and creates the carousel component, adding predefined items to it.
     *
     * @return void
     */
    protected function carousel_Create(): void
    {
        $this->carousel = new Bs\Carousel ($this);
        $this->carousel->addListItem(new Bs\CarouselItem('cat.jpg', 'Cat'));
        $this->carousel->addListItem(new Bs\CarouselItem('rhino.jpg', 'Rhino'));
        $this->carousel->addListItem(new Bs\CarouselItem('pig.jpg', 'Pig'));
    }

    /**
     * Initializes and creates the accordion component.
     *
     * @return void
     */
    protected function accordion_Create(): void
    {
        $this->accordion = new Bs\Accordion($this);
        $this->accordion->setDataBinder("Accordion_Bind");
        $this->accordion->setDrawingCallback([$this, "Accordion_Draw"]);
    }

    /**
     * Binds the data source for the accordion control by loading all Person objects
     * with their associated Address data expanded.
     *
     * @return void
     */
    protected function accordion_Bind(): void
    {
        $this->accordion->DataSource = Person::loadAll([QQ::expand(QQN::person()->Address)]);
    }

    /**
     * Renders specific parts of an accordion UI element based on the given part type.
     *
     * @param object $objAccordion The accordion object responsible for rendering the UI.
     * @param string $strPart The part of the accordion to be rendered, such as header or body.
     * @param object $objItem The data item containing information for rendering the accordion part.
     * @param int $intIndex The index of the current item in the accordion.
     *
     * @return void
     */
    public function accordion_Draw(object $objAccordion, string $strPart, object $objItem, int $intIndex): void
    {
        switch ($strPart) {
            case Bs\Accordion::RENDER_HEADER:
                $objAccordion->renderToggleHelper(QString::htmlEntities($objItem->FirstName . ' ' . $objItem->LastName));
                break;

            case Bs\Accordion::RENDER_BODY:
                if ($objItem->Address) {
                    echo "<b>Address: </b>" . $objItem->Address->Street . ", " . $objItem->Address->City;
                }
                break;
        }
    }

    /**
     * Creates and initializes radio list components with specified items and configurations.
     *
     * @return void
     */
    protected function radioList_Create(): void
    {
        $this->lstRadio1 = new Bs\RadioList($this);
        $this->lstRadio1->addItems(["yes" => "Yes", "no" => "No"]);

        $this->lstRadio2 = new Bs\RadioList($this);
        $this->lstRadio2->addItems(["yes" => "Yes", "no" => "No"]);
        $this->lstRadio2->ButtonMode = RadioButtonList::BUTTON_MODE_SET;
        $this->lstRadio2->ButtonStyle = Bs\Bootstrap::BUTTON_PRIMARY;

    }

    /**
     * Creates and initializes dropdown components with specified items and configurations.
     *
     * @return void
     */
    protected function dropdowns_Create(): void
    {
        $selItems = [
            new Bs\DropdownItem("First"),
            new Bs\DropdownItem("Second"),
            new Bs\DropdownItem("Third")

        ];
        $this->lstPlain = new Bs\Dropdown($this);
        $this->lstPlain->Text = "Plain";
        $this->lstPlain->addItems($selItems);
    }


}

SampleForm::run('SampleForm');
