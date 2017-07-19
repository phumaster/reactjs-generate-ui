/**
 * This component auto generate by PhuMaster Generate Script v0.0.1
 * @author PhuMaster
 * @class LoginForm
 * @createdAt Wed, 19 Jul 2017 10:31:55 +0700
 */

import React, { Component } from 'react';
import { Field, reduxForm, formValueSelector } from 'redux-form';
import { connect } from 'react-redux';

const validate = values => {
  const errors = {}
  
  let username = values.username;
  if (!username) {
    errors.username = "Please input this field";
  }
else  if (username.length < 6) {
    errors.username = "This field must be > 6";
  }
else  if (username.length < 6 && username.length > 12) {
    errors.username = "Value must be > 6 and < 12";
  }
else  if (username !== values.password) {
    errors.username = "2 password not match";
  }

  let password = values.password;
  if (!password) {
    errors.password = "Please input this field";
  }
else  if (password.length < 6) {
    errors.password = "This field must be > 6";
  }
else  if (password.length < 6 && password.length > 12) {
    errors.password = "Value must be > 6 and < 12";
  }
else  if (password !== values.password) {
    errors.password = "2 password not match";
  }

  let gender = values.gender;

  return errors
}

const warn = values => {
  // const warnings = {}
  // if (values.age < 19) {
  //   warnings.age = 'Hmm, you seem a bit young...'
  // }
  return {}
}

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

class LoginForm extends Component {
  constructor(props) {
    super(props);
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true;
  }

  render() {
    const { handleSubmit, pristine, reset, submitting } = this.props;
    return (
      <form onSubmit={handleSubmit}>
                  <Field
            name="username"
            type="text"
            component={renderField}
            label="Username"
            className=""
          />
          <Field
            name="password"
            type="password"
            component={renderField}
            label="Password"
            className=""
          />
          <Field
            name="gender"
            component="select"
            label="Gender"
            className=""
          ></Field>

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
}

LoginForm = reduxForm({
  form: 'LoginForm',
  validate,
  warn
})(LoginForm);

const selector = formValueSelector('LoginForm');

const mapStateToProps = state => {
  return {
    initialValues: {}
  };
};

LoginForm = connect(mapStateToProps)(LoginForm);

export default LoginForm