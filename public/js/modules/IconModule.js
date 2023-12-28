const IconModule = (function () {
    async function render(iconName) {
        const svgFilePath = `public/assets/svg/${iconName}.svg`;

        try {
            const response = await fetch(svgFilePath);
            if (!response.ok) {
                throw new Error(`Failed to fetch SVG file: ${response.statusText}`);
            }

            return response.text();
        } catch (error) {
            console.error(`Error reading SVG file: ${error.message}`);
            return "";
        }
    }

    return {
        render: render
    }
}());

export default IconModule;