class ApiClient {
    static createResultObject(success, data, message) {
        return { success, data, message };
    }

    static handleApiResponse(jsonResponse) {
        if (jsonResponse.status === "success") {
            return this.createResultObject(true, jsonResponse.data);
        } else {
            const errorMessage = jsonResponse.message || 'Oops! Something went wrong';
            console.error(`API ${jsonResponse.status}:`, jsonResponse.message, jsonResponse.data);
            return this.createResultObject(false, jsonResponse.data, errorMessage);
        }
    }
    
    static async fetchData(url, options = {}) {
        if (!options.headers)
            options.headers = new Headers();
        
        options.headers.set("Content-Type", "application/json");
        
        try {
            const response = await fetch(url, options);
            const jsonResponse = await response.json();
            return this.handleApiResponse(jsonResponse);
        } catch (error) {
            const result = this.createResultObject(false, null, 'Unknown error occurred.');
            console.error('API error:', result.message, result.data);
            return result;
        }
    }
}

export default ApiClient;
