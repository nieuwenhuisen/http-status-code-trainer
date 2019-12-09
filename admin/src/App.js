import React from "react";
import { HydraAdmin } from "@api-platform/admin";
import { Layout, Resource } from 'react-admin';

// Import pages
import { LoginPage } from './pages/login';
import { UserListPage, UserEditPage } from './pages/user';
import { StatusCodeListPage, StatusCodeEditPage } from './pages/status_code';

import { authProvider, dataProvider } from './admin';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;

export default () => (
    <HydraAdmin
        dataProvider={dataProvider}
        loginPage={LoginPage}
        authProvider={authProvider(entrypoint)}
        entrypoint={entrypoint}
        appLayout={Layout}
        >
            <Resource name="users" list={UserListPage} edit={UserEditPage} />
            <Resource name="status_codes" list={StatusCodeListPage} edit={StatusCodeEditPage} />
    </HydraAdmin>
);
