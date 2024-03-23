import ApiClient from "../dashboard/ApiClient.js"

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

    static getFullUrl(url) {
        return url.match("^(.*)://") ? url : "https://" + url;
    }

    static async getPageTitle(url, maxchars = 50) {
        return await ApiClient
            .fetchData(`/util/webtitle?url=${url}`)
            .then(res => {
                let result;
                
                if (res.success){
                    result = res.data.title;
                }
                else{
                    console.log("Page title fetch error! ", res.message);
                    result = this.getDomainName(url);
                }
                    
                return result.substring(0, maxchars);
            });
    }
}

export default StringHelper;
