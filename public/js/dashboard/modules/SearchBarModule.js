import ButtonModule from "./ButtonModule.js";

const SearchBarModule = (function () {
    async function render(callback) {
        let searchBar = document.createElement("div");
        searchBar.innerHTML = `
            <div class="search-container flex flex-center hide-mobile">
                <input type="text" name="search" class="input" placeholder="Search">
            </div>`
        searchBar = searchBar.firstElementChild;

        searchBar.appendChild(await ButtonModule.render("search", callback, "btn-menu"));
        return searchBar;
    }

    return {
        render: render
    };
}());

export default SearchBarModule;
