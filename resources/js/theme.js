import { createTheme } from '@mui/material/styles';
import { red } from '@mui/material/colors';

// Create a theme instance.
const theme = createTheme({
    cssVariables: true,
    typography: {
        fontFamily: 'Itim, Helvetica, Arial, sans',
    },
    palette: {
        background: {
//            default: '#191619'
        },
        primary: {
            main: '#fa1a8a',
        },
        secondary: {
            main: '#1afafa',
        },
        text: {
            primary: '#1f1f1f',
        },
        error: {
            main: red.A400,
        },
    },
});

export default theme;
