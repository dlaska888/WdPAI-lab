class StringHelper {
    static getDomainName(url) {
        try {
            const parsedURL = new URL(url);
            const hostname = parsedURL.hostname;

            // Remove any leading 'www.' and return the remaining part with the first letter capitalized
            let domainWithoutWww = hostname.replace(/^www\./, '');

            // Remove any generic domain suffix
            const parts = domainWithoutWww.split('.');
            if (parts.length > 1) {
                parts.pop(); // Remove the last part (suffix)
            }

            return StringHelper.capitalizeFirstLetter(parts.join('.'));
        } catch (error) {
            console.error('Error parsing URL:', error.message);
            return null;
        }
    }

    static capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    static getPageTitle(url) {
        const requestOptions = {
            method: 'GET',
            headers: new Headers({
                'Content-Type': 'text/html',  // Set the content type according to your needs
                'Access-Control-Allow-Origin': '*'  // Allow requests from any origin, adjust as needed
            }),
        };

        return fetch(url, requestOptions)
            .then(function(response) {
                return response.text();
            })
            .then(function(body) {
                return body.split('<title>')[1].split('</title>')[0];
            })
            .catch(() => this.getDomainName(url));
    }
}

export default StringHelper;
