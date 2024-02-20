import ApiClient from "./ApiClient.js";

class SessionRefresher {
    #endpoint = "/refreshSession";
    #refreshInterval = 300000; // 5 minutes in milliseconds
    refreshSessionInterval;

    constructor() {
        this.refreshSessionInterval = setInterval(async () => await this.refreshSession(), this.#refreshInterval);
    }

    async refreshSession() {
        await ApiClient.fetchData(this.#endpoint, { method: "POST" });
        console.log("xd");
    }

}

export default SessionRefresher;
