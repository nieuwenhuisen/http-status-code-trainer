import React from 'react';
import { InputGuesser } from '@api-platform/admin';
import { Edit, SimpleForm } from 'react-admin';

export default (props) => (
    <Edit undoable={false} {...props}>
        <SimpleForm redirect="list">
            <InputGuesser source="email" />
        </SimpleForm>
    </Edit>
);
