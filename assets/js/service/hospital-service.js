import axios from 'axios';

/**
 * @returns {Promise<AxiosResponse<any>>}
 */
export function fetchAllHospitals() {
    return axios.get('/api/hospitals.jsonld');
}
