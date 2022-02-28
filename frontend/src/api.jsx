import axios from "axios";

const BASE_URL = 'http://localhost:8070'

const GetDashboardData = () => {
    return axios
        .get(`${BASE_URL}/api/dashboard`)
        .then(response => response.data)
        .catch(error => {
            throw error;
        });
}

export { GetDashboardData }