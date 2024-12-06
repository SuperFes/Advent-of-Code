import '../css/app.css';
import './bootstrap';

import '@fontsource/itim/400.css';
import '@fontsource/henny-penny/400.css';

import * as React             from 'react';
import {createInertiaApp}     from '@inertiajs/react';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createRoot}           from 'react-dom/client';
import CssBaseline            from '@mui/material/CssBaseline';
import {ThemeProvider}        from '@mui/material/styles';
import theme                  from './theme';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title  : (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
            import.meta.glob('./Days/**/Day*.jsx'),
        ),
    setup({el, App, props}) {
        const root = createRoot(el);

        root.render(
            <React.StrictMode>
                <ThemeProvider theme={theme}>
                    <CssBaseline />
                    <App {...props} />
                </ThemeProvider>
            </React.StrictMode>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
