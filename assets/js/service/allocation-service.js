import axios from 'axios';

/**
 * @param currentPage
 * @param perPage
 * @returns {Promise<AxiosResponse<any>>}
 */
export function fetchAllocations(currentPage, perPage) {
    const params = {};

    if (currentPage) {
        params.page = currentPage;
    }

    if (perPage) {
        params.itemsPerPage = perPage;
    }

    return axios.get('/api/allocations.jsonld', {
        params,
    });
}
