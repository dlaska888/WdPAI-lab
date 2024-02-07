class ApiClient {
    static createResultObject(success, data, message) {
        return { success, data, message };
    }

    static handleApiResponse(jsonResponse) {
        if (jsonResponse.status === "success") {
            return this.createResultObject(true, jsonResponse.data);
        }

        const errorMessage = jsonResponse.message || 'Oops! Something went wrong';
        console.error(`API ${jsonResponse.status}:`, jsonResponse.message, jsonResponse.data);
        return this.createResultObject(false, jsonResponse.data, errorMessage);
    }

    static async fetchData(url, options = {}) {
        if (!options.headers)
            options.headers = new Headers();
        
        // Add "Accept" header to indicate that the client accepts JSON
        options.headers.append('Accept', 'application/json');
        options.mode = "same-origin";

        try {
            const response = await fetch(url, options);
            const jsonResponse = await response.json();
            return this.handleApiResponse(jsonResponse);
        } catch (error) {
            const result = this.createResultObject(false, null, 'Oops! Something went wrong');
            console.error('API error:', error.message);
            return result;
        }
    }

    static async fetchFile(url, options = {}) {
        options.mode = "same-origin";

        try {
            const response = await fetch(url, options);
            if (response.ok)
                return this.createResultObject(true, await response.blob());
            
            return this.createResultObject(false, null, response.message)
            
        } catch (error) {
            const result = this.createResultObject(false, null, 'Oops! Something went wrong');
            console.error('API error:', error.message);
            return result;
        }
    }
}

export default ApiClient;
