import React from 'react';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import classnames from 'classnames';
import { withTranslate, userLogin } from 'ra-core';
import { Field, reduxForm } from 'redux-form';
import {
    CircularProgress, TextField, Button, CardActions, Card, MuiThemeProvider, createMuiTheme, withStyles,
} from '@material-ui/core';
import { Notification } from 'react-admin';
import styles from './login.styles';

const renderInput = ({ meta: { touched, error } = { touched: false, error: '' }, input: { ...inputProps }, ...props }) => (
    <TextField
        error={!!(touched && error)}
        helperText={touched && error}
        {...inputProps}
        {...props}
        fullWidth
    />
);

const login = (auth, dispatch, { redirectTo }) => dispatch(userLogin(auth, redirectTo));

const LoginPage = ({ handleSubmit, isLoading, classes, translate, className, theme }) => (
    <MuiThemeProvider theme={createMuiTheme(theme)}>
        <Notification />

        <div className={classnames(classes.main, className)}>
            <Card className={classes.card}>
                <div className={classes.logo}>
                    ADMIN
                </div>

                <form onSubmit={handleSubmit(login)} className="form-signin" autoComplete="off">
                    <div className={classes.form}>
                        <div className={classes.input}>
                            <Field
                                autoFocus
                                id="username"
                                name="username"
                                component={renderInput}
                                label={translate('ra.auth.username')}
                                disabled={isLoading}
                            />
                        </div>

                        <div className={classes.input}>
                            <Field
                                id="password"
                                name="password"
                                component={renderInput}
                                label={translate('ra.auth.password')}
                                type="password"
                                disabled={isLoading}
                            />
                        </div>
                    </div>

                    <CardActions>
                        <Button
                            variant="raised"
                            type="submit"
                            color="primary"
                            disabled={isLoading}
                            className={classes.button}
                        >
                            {isLoading && (
                                <CircularProgress
                                    className={classes.icon}
                                    size={18}
                                    thickness={2}
                                />
                            )}
                            {translate('ra.auth.sign_in')}
                        </Button>
                    </CardActions>
                </form>
            </Card>
        </div>
    </MuiThemeProvider>
);

const mapStateToProps = (state) => ({
    isLoading: state.admin.loading > 0,
});

export default compose(
    withStyles(styles),
    withTranslate,
    connect(mapStateToProps),
    reduxForm({
        form: 'signIn',
        validate: (values, props) => {
            const errors = { username: '', password: '' };
            const { translate } = props;
            if (!values.username) {
                errors.username = translate('ra.validation.required');
            }
            if (!values.password) {
                errors.password = translate('ra.validation.required');
            }
            return errors;
        },
    }),
)(LoginPage);
