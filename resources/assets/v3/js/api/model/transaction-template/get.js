import { api } from "../../../boot/axios";

export default class Get {
    /**
     *
     * @param params
     * @returns {Promise<AxiosResponse<any>>}
     */
    list(params) {
        return api.get("/api/v1/transaction-templates", { params: params });
    }
}
