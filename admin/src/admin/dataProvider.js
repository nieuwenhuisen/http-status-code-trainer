import { fetchHydra as baseFetchHydra } from '@api-platform/admin';
import parseHydraDocumentation from '@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation';
import baseDataProvider from '@api-platform/admin/src/hydra/dataProvider';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;

const headers = () => {
    const values = {};

    if (window.localStorage.getItem('token')) {
        values.Authorization = `Bearer ${window.localStorage.getItem('token')}`;
    }

    return new Headers(values);
};

const logout = () => {
    window.localStorage.removeItem('token');
    window.location.href = '/';
};

const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
    ...options,
    headers: headers(),
})
    .catch((data) => {
        if (typeof data.response !== 'undefined' && typeof data.response.status !== 'undefined' && data.response.status === 401) {
            logout();
            return;
        }

        return Promise.reject(data);
    });

const apiDocumentationParser = (entrypoint) => parseHydraDocumentation(entrypoint, { headers: headers() })
    .then(
        ({ api }) => ({ api }),
        (result) => {
            if (result.status === 401) {
                logout();
            }
            return Promise.reject(result);
        },
    );

export default baseDataProvider(entrypoint, fetchHydra, apiDocumentationParser);
