import { createStyles } from '@material-ui/core';

export default ({ spacing }) => createStyles({
    main: {
        display: 'flex',
        flexDirection: 'column',
        minHeight: '100vh',
        height: '1px',
        alignItems: 'center',
        justifyContent: 'flex-start',
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
    },
    logo: {
        textAlign: 'center',
    },
    card: {
        minWidth: 300,
        marginTop: '6em',
        padding: '2em',
    },
    form: {
        padding: '0 16px',
    },
    login: {
        margin: '1em',
        display: 'flex',
        justifyContent: 'center',
    },
    input: {
        marginTop: '1em',
    },
    button: {
        width: '100%',
        marginTop: '3em',
    },
    icon: {
        marginRight: spacing.unit,
    },
});
