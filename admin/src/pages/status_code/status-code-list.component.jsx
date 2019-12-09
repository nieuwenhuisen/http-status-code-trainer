import React from 'react';
import { Filter, Datagrid, List, TextField } from 'react-admin';

const StatusCodeFilter = (props) => (
    <Filter {...props}></Filter>
);

export default (props) => (
    <List {...props} filters={<StatusCodeFilter />}>
        <Datagrid rowClick="edit">
            <TextField source="code" label="Code" />
            <TextField source="title" label="Title" />
        </Datagrid>
    </List>
);
