<?php

namespace Kendo\UI;

class MaskedTextBox extends \Kendo\UI\Widget {
    protected function name() {
        return 'MaskedTextBox';
    }

    protected function createElement() {
        return new \Kendo\Html\Element('input', true);
    }
//>> Properties

    /**
    * Specifies whether the widget will replace the prompt characters with spaces on blur. Prompt chars will be shown again on focus (available since Q2 2014 SP1).
    * @param boolean $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function clearPromptChar($value) {
        return $this->setProperty('clearPromptChar', $value);
    }

    /**
    * Specifies the culture info used by the widget.
    * @param string $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function culture($value) {
        return $this->setProperty('culture', $value);
    }

    /**
    * Specifies the input mask. The following mask rules are supported: 0 - Digit. Accepts any digit between 0 and 9.; 9 - Digit or space. Accepts any digit between 0 and 9, plus space.; # - Digit or space. Like 9 rule, but allows also (+) and (-) signs.; L - Letter. Restricts input to letters a-z and A-Z. This rule is equivalent to [a-zA-Z] in regular expressions.; ? - Letter or space. Restricts input to letters a-z and A-Z. This rule is equivalent to [a-zA-Z] in regular expressions.; & - Character. Accepts any character. The rule is equivalent to \S in regular expressions.; C - Character or space. Accepts any character. The rule is equivalent to . in regular expressions.; A - Alphanumeric. Accepts letters and digits only.; a - Alphanumeric or space. Accepts letters, digits and space only.; . - Decimal placeholder. The decimal separator will be gotten from the current culture used by Kendo.; , - Thousands placeholder. The display character will be gotten from the current culture used by Kendo. or $ - Currency symbol. The display character will be gotten from the current culture used by Kendo..
    * @param string $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function mask($value) {
        return $this->setProperty('mask', $value);
    }

    /**
    * Specifies the character used to represent the absence of user input in the widget
    * @param string $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function promptChar($value) {
        return $this->setProperty('promptChar', $value);
    }

    /**
    * Defines an object of custom mask rules.
    * @param  $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function rules($value) {
        return $this->setProperty('rules', $value);
    }

    /**
    * Specifies whether the widget will unmask the input value on form post (available since Q1 2015).
    * @param boolean $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function unmaskOnPost($value) {
        return $this->setProperty('unmaskOnPost', $value);
    }

    /**
    * Specifies the value of the MaskedTextBox widget.
    * @param string $value
    * @return \Kendo\UI\MaskedTextBox
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Sets the change event of the MaskedTextBox.
    * Fires when the value is changed
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\MaskedTextBox
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }


//<< Properties
}

?>
