import React from 'react';
import { Filter, Datagrid, List, TextField } from 'react-admin';

const UserFilter = (props) => (
    <Filter {...props}></Filter>
);

export default (props) => (
    <List {...props} filters={<UserFilter />}>
        <Datagrid rowClick="edit">
            <TextField source="originId" sortBy="id" label="ID" />
            <TextField source="email" label="E-mail address" />
        </Datagrid>
    </List>
);
