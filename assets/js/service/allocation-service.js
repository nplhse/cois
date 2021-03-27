import axios from 'axios';

/**
 * @param currentPage
 * @param perPage
 * @returns {Promise<AxiosResponse<any>>}
 */
export function fetchAllocations(currentPage, perPage, sortBy, sortDesc, filter, filterOn) {
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
        sortBy = 'sK';
    }

    if (filter && filter !== '') {
        for (const target in filterOn) {
            params[`${filterOn[target]}`] = filter;
        }
    }

    params[`order[${sortBy}]`] = sortDesc ? 'asc' : 'desc';

    return axios.get('/api/allocations.jsonld', {
        params,
    }).catch((error) => {
        console.log('\n\n\n\n');
        console.log(error);
    });
}
