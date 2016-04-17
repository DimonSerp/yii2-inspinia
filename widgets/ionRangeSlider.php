<?php

namespace maxyc\inspinia\widgets;

    use Yii;
    use maxyc\inspinia\ionRangeSliderAsset;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use yii\jui\InputWidget;

    /**
 * Class Panel
 *
 * @package maxyc\inspinia\widgets
 */
class ionRangeSlider extends InputWidget
{
    /**
     * @var string the locale ID (e.g. 'fr', 'de', 'en-GB') for the language to be used by the date picker.
     * If this property is empty, then the current application language will be used.
     *
     * Since version 2.0.2 a fallback is used if the application language includes a locale part (e.g. `de-DE`) and the language
     * file does not exist, it will fall back to using `de`.
     */
    public $language;

    /**
     * @var string the format string to be used for formatting the date value. This option will be used
     * to populate the [[clientOptions|clientOption]] `dateFormat`.
     * The value can be one of "short", "medium", "long", or "full", which represents a preset format of different lengths.
     *
     * It can also be a custom format as specified in the [ICU manual](http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax).
     * Alternatively this can be a string prefixed with `php:` representing a format that can be recognized by the
     * PHP [date()](http://php.net/manual/de/function.date.php)-function.
     *
     * For example:
     *
     * ```php
     * 'MM/dd/yyyy' // date in ICU format
     * 'php:m/d/Y' // the same date in PHP format
     * ```
     *
     * If not set the default value will be taken from `Yii::$app->formatter->dateFormat`.
     */
    public $dateFormat;

    /**
     * @var string the model attribute that this widget is associated with.
     * The value of the attribute will be converted using [[\yii\i18n\Formatter::asDate()|`Yii::$app->formatter->asDate()`]]
     * with the [[dateFormat]] if it is not null.
     */
    public $attribute;
    /**
     * @var string the input value.
     * This value will be converted using [[\yii\i18n\Formatter::asDate()|`Yii::$app->formatter->asDate()`]]
     * with the [[dateFormat]] if it is not null.
     */
    public $value;


    public function init()
    {
        parent::init();

        if ($this->dateFormat === null) {
            $this->dateFormat = Yii::$app->formatter->dateFormat;
        }

        ionRangeSliderAsset::register($this->view);

        return true;
    }

    public function run()
    {
        echo $this->renderWidget() . "\n";

        $containerID = $this->options['id'];
        $options = Json::htmlEncode($this->clientOptions);

        $this->view->registerJs("$('#{$containerID}').ionRangeSlider({$options});");

    }

    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asDate($value, $this->dateFormat);
            } catch(InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }

        $options = $this->options;
        $options['value'] = $value;


        // render an inline date picker with hidden input
        if ($this->hasModel()) {
            $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
        } else {
            $contents[] = Html::hiddenInput($this->name, $value, $options);
        }
//        $this->clientOptions['defaultDate'] = $value;
//        $this->clientOptions['altField'] = '#' . $this->options['id'];
//        $contents[] = Html::tag('div', null, $this->containerOptions);


        return implode("\n", $contents);
    }
}
