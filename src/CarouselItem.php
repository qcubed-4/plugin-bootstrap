<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\HListItem;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class CarouselItem
 *
 * An item to add as a child control to a Carousel
 *
 * @property string $ImageUrl Url of the image to show here
 * @property string $AltText Alt text to show for the image
 * @package QCubed\Bootstrap
 */
class CarouselItem extends HListItem {
    protected string $strImageUrl;
    protected ?string $strAltText;

    /**
     * Constructor method for initializing the object.
     *
     * @param string $strImageUrl The URL of the image.
     * @param string|null $strAltText Optional alternative text for the image. Default is null.
     * @param string $strText The text content of the object. Default is an empty string.
     * @param mixed $strAnchor The anchor value associated with the text. Default is null.
     * @param string|null $strId An optional identifier for the object. Default is null.
     *
     * @return void
     */
    public function __construct(string $strImageUrl, ?string $strAltText = null, string $strText = '', mixed $strAnchor = null, ?string $strId = null) {
        parent::__construct($strText, $strAnchor, $strId);
        $this->strImageUrl = $strImageUrl;
        $this->strAltText = $strAltText;
    }

    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the requested property, or the value from the parent class if not explicitly handled.
     *
     * @throws Caller If the property does not exist or is inaccessible, an exception is thrown.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "ImageUrl": return $this->strImageUrl;
            case "AltText": return $this->strAltText;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Magic method for setting the value of a property dynamically.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     *
     * @throws InvalidCast If the value cannot be cast to the required type.
     * @throws Caller If the parent::__set method encounters an error.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "ImageUrl":
                try {
                    $this->strImageUrl = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "AltText":
                try {
                    $this->strAltText = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}