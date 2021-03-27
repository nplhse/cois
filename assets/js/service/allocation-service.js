import axios from 'axios';

/**
 * @param currentPage
 * @param perPage
 * @returns {Promise<AxiosResponse<any>>}
 */
export function fetchAllocations(currentPage, perPage, sortBy, sortDesc, filters) {
    const params = {};

    params.page = currentPage;
    params.itemsPerPage = perPage;

    if (sortBy === 'hospital') {
        sortBy = 'hospital.name';
    }

    if (sortBy === 'times') {
        sortBy = 'createdAt';
    }

    if (sortBy === 'urgency') {
        sortBy = 'SK';
    }

    params[`order[${sortBy}]`] = sortDesc ? 'asc' : 'desc';

    if (filters.search && filters.search !== '') {
        for (const target in filters.fields) {
            params[`${filters.fields[target]}`] = filters.search;
        }
    }

    if (filters.dateAfter) {
        params[`createdAt[after]`] = filters.dateAfter;
    }

    if (filters.dateBefore) {
        params[`createdAt[before]`] = filters.dateBefore;
    }

    for (const key in filters.properties) {
        if (filters.properties[key]) {
            params[`${key}`]  = true;
        }
    }

    return axios.get('/api/allocations.jsonld', {
        params,
    }).catch((error) => {
        console.log('\n\n\n\n');
        console.log(error);
    });
}
