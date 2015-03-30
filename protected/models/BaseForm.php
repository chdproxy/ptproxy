<?php
class BaseForm extends CFormModel {

  public function validate($attributes = NULL, $clearErrors = TRUE) {
    if ($clearErrors) {
      $this->clearErrors();
    }
    if ($this->beforeValidate()) {
      foreach ($this->getValidators() as $validator) {
        if ($this->hasErrors()) {
          break;
        }
        $validator->validate($this, $attributes);
      }
      $this->afterValidate();
      return !$this->hasErrors();
    }
    else {
      return FALSE;
    }
  }
}