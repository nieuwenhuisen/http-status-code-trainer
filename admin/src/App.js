import React from "react";
import { HydraAdmin } from "@api-platform/admin";
import { Layout } from 'react-admin';

import { LoginPage } from './pages/login';

import authProvider from './admin/authProvider';
import dataProvider from './admin/dataProvider';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;

export default () => (
    <HydraAdmin
        dataProvider={dataProvider}
        loginPage={LoginPage}
        authProvider={authProvider(entrypoint)}
        entrypoint={entrypoint}
        appLayout={Layout}
        >
    </HydraAdmin>
);
