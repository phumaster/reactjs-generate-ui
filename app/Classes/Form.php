<?php

namespace PhuMaster\Classes;

class Form {

  protected $form = [];

  public function __construct($form = []) {
    $this->form = $form;
  }

  public function getFormName(): string {
    return $this->form['name'];
  }

  public function field($props = []): string {
    if ($props['type'] === 'select') {
      return <<<INPUT
          <Field
            name="{$props['name']}"
            component="select"
            label="{$props['label']}"
            className="{$props['className']}"
          ></Field>

INPUT;
    } else {
      return <<<INPUT
          <Field
            name="{$props['name']}"
            type="{$props['type']}"
            component={renderField}
            label="{$props['label']}"
            className="{$props['className']}"
          />

INPUT;
    }
  }

  public function button(): string {
    return <<<BUTTON
        <button {$this->propsString}></button>
BUTTON;
  }

  public function importReact(): string {
    return "import React, { Component } from 'react';";
  }

  public function import(): string {
    return <<<IMPORT
{$this->importReact()}
{$this->importReduxForm()}
{$this->importConnect()}
IMPORT;
  }

  public function importConnect(): string {
    if ($this->form['hasSelect']) {
      return "import { connect } from 'react-redux';";
    }
    return '';
  }

  public function importReduxForm(): string {
    $componentReduxForm = [
      'Field',
      'reduxForm'
    ];

    if ($this->form['hasFieldArray']) {
      $componentReduxForm[] = 'FieldArray';
    }
    if ($this->form['hasSelect']) {
      $componentReduxForm[] = 'formValueSelector';
    }
    return "import { ".implode(', ', $componentReduxForm)." } from 'redux-form';";
  }

  public function validate(): string {
    $fields = $this->form['fields'];
    $validateRules = $this->form['validate'];
    $stringRule = '';
    $currentField = null;

    foreach ($fields as $key => $field) {
      $fieldName = $field['name'];

      if ($currentField === $fieldName) {
        $currentField = null;
      }
      $stringRule .= <<<fieldName

  let {$fieldName} = values.{$fieldName};

fieldName;

      $rule = isset($validateRules[$fieldName]) ? $validateRules[$fieldName] : null;

      if (!is_null($rule)) {
        if (isset($rule['required'])) {
          $currentField = $fieldName;
          $stringRule .= <<<REQUIRED
  if (!{$fieldName}) {
    errors.{$fieldName} = "{$validateRules[$fieldName]['required']['message']}";
  }

REQUIRED;
        }

        if (isset($rule['minLength']) && $rule['minLength']['value'] !== -1) {
          if (!is_null($currentField)) {
            $stringRule .= 'else';
          }
          $stringRule .= <<<minLength
  if ({$fieldName}.length < {$rule['minLength']['value']}) {
    errors.{$fieldName} = "{$validateRules[$fieldName]['minLength']['message']}";
  }

minLength;
        }

        if (isset($rule['maxLength']) && $rule['maxLength']['value'] !== -1) {
          if (!is_null($currentField)) {
            $stringRule .= 'else';
          }
          $stringRule .= <<<maxLength
  if ({$fieldName}.length > {$rule['maxLength']['value']}) {
    errors.{$fieldName} = "{$validateRules[$fieldName]['maxLength']['message']}";
  }

maxLength;
        }

        if (isset($rule['between'])) {
          if (!is_null($currentField)) {
            $stringRule .= 'else';
          }
          $between = explode('|', $rule['between']['value']);
          $stringRule .= <<<between
  if ({$fieldName}.length < {$between[0]} && {$fieldName}.length > {$between[1]}) {
    errors.{$fieldName} = "{$validateRules[$fieldName]['between']['message']}";
  }

between;
        }

        if (isset($rule['same'])) {
          if (!is_null($currentField)) {
            $stringRule .= 'else';
          }
          $stringRule .= <<<same
  if ({$fieldName} !== values.{$rule['same']['value']}) {
    errors.{$fieldName} = "{$validateRules[$fieldName]['same']['message']}";
  }

same;
        }

      }
    }

    return <<<VALIDATE
const validate = values => {
  const errors = {}
  {$stringRule}
  return errors
}
VALIDATE;
  }

  public function warning(): string {
    return <<<WARNING
const warn = values => {
  // const warnings = {}
  // if (values.age < 19) {
  //   warnings.age = 'Hmm, you seem a bit young...'
  // }
  return {}
}
WARNING;
  }

  public function renderField(): string {
    return <<<RENDERFIELD
const renderField = ({ input, label, type, meta: { touched, error } }) => {
  return (
    <div>
      <label>
        {label}
      </label>
      <div>
        <input {...input} placeholder={label} type={type} />
        {touched &&
          error &&
          <span>
            {error}
          </span>}
      </div>
    </div>
  )
}
RENDERFIELD;
  }

  public function render(): string {
    $chuck = '';

    foreach ($this->form['fields'] as $key => $field) {
      $chuck .= $this->field([
        'name' => $field['name'],
        'type' => $field['type'],
        'label' => $field['label'],
        'className' => $field['className'] ?? '',
      ]);
    }
    return <<<RENDER
  render() {
    const { handleSubmit, pristine, reset, submitting } = this.props;
    return (
      <form onSubmit={handleSubmit}>
        {$chuck}
        <div>
          <button type="submit" disabled={submitting}>
            Submit
          </button>
          <button type="button" disabled={pristine || submitting} onClick={reset}>
            Clear
          </button>
        </div>
      </form>
    )
  }
RENDER;
  }

  public function shouldComponentUpdate(): string {
    return <<<SHOULDCOMPONENT
  shouldComponentUpdate(nextProps, nextState) {
    return true;
  }
SHOULDCOMPONENT;
  }

  public function renderComment(): string {
    $name = ucfirst($this->getFormName());
    $time = date("r");
    return <<<COMMENT
/**
 * This component auto generate by PhuMaster Generate Script v0.0.1
 * @author PhuMaster
 * @class $name
 * @createdAt $time
 */
COMMENT;
  }

  public function export(): string {
    $extra = '';
    if ($this->form['hasSelect']) {
      $extra = <<<EXTRA
const selector = formValueSelector('{$this->getFormName()}');

const mapStateToProps = state => {
  return {
    initialValues: {}
  };
};

{$this->getFormName()} = connect(mapStateToProps)({$this->getFormName()});
EXTRA;
    }
    return <<<EXPORT
{$this->getFormName()} = reduxForm({
  form: '{$this->getFormName()}',
  validate,
  warn
})({$this->getFormName()});

{$extra}

export default {$this->getFormName()}
EXPORT;
  }

}
