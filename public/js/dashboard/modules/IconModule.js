const IconModule = (function () {
    let iconCache = {}; //TODO is this a good practice?
    
    function render(iconName) {
        const svgFilePath = `public/assets/svg/${iconName}.svg`;
        
        if (iconCache[iconName]){
            return iconCache[iconName];
        }

        return fetch(svgFilePath)
            .then(async response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch SVG file: ${response.statusText}`);
                }
                
                const icon = response.text();
                iconCache[iconName] = await icon;
                console.log("fetched!");
                
                return icon;
            })
            .catch(error => {
                console.error(`Error reading SVG file: ${error.message}`);
                return "";
            });
    }

    return {
        render: render
    }
}());

export default IconModule;