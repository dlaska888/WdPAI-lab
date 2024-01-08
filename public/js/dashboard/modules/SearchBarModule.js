import ButtonModule from "./ButtonModule.js";
import ApiClient from "../ApiClient.js";
import GroupModule from "./GroupModule.js";
import NotificationService from "../NotificationService.js";

const SearchBarModule = (function () {
    async function render(pageId, groupsEndpoint) {
        let searchBar = document.createElement("div");
        searchBar.innerHTML = `
            <form class="search-container flex flex-center hide-mobile">
                <input type="text" name="name" class="input" placeholder="Search">
            </form>`
        searchBar = searchBar.firstElementChild;

        searchBar.appendChild(await ButtonModule.render("search", null, "btn-menu"));
        searchBar.addEventListener("submit", (e) => {
            e.preventDefault();
            searchGroups(e, pageId, groupsEndpoint);
        })
        searchBar.querySelector(".btn-menu").type = "submit";
        
        return searchBar;
    }

    function searchGroups(e, pageId, groupsEndpoint) {
        const formData = new FormData(e.currentTarget);

        ApiClient.fetchData(`${groupsEndpoint}/search?name=${formData.get("name")}`)
            .then(async response => {
                if (response.success) {
                    const groupsContainer = document
                        .querySelector(`[id="${pageId}"]`)
                        .querySelector('.groups-container');

                    if (groupsContainer) {
                        groupsContainer.innerHTML = "";
                        for (const group of response.data) {
                            groupsContainer.appendChild(await GroupModule.render(group));
                        }
                    } else {
                        NotificationService.notify("Could not load groups", "error");
                    }
                }
            });
    }
    

    return {
        render: render
    };
}());

export default SearchBarModule;
