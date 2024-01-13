import ApiClient from "../ApiClient.js";
import GroupModule from "./GroupModule.js";
import NotificationService from "../NotificationService.js";

const GroupSearchModule = (function () {
    let searchTimer; // Variable to store the timer
    let loading = false;
    
    async function render(pageId, groupsEndpoint) {
        let searchBar = document.createElement("div");
        searchBar.innerHTML = `
        <form class="search-container flex flex-center hide-mobile">
            <input type="text" name="name" class="input" placeholder="Search groups...">
        </form>`
        searchBar = searchBar.firstElementChild;

        searchBar.addEventListener("input", () => {
            clearTimeout(searchTimer); // Clear the previous timer
            searchTimer = setTimeout(() => {
                search(searchBar, pageId, groupsEndpoint);
            }, 500); // Adjust the delay as needed
        });

        return searchBar;
    }

    async function search(form, pageId, groupsEndpoint) {
        if (loading) {
            return; // Do nothing if the button is disabled
        }

        loading = true; // Disable the button to prevent spamming

        toggleSpinner(true);

        clearPage();
        const formData = new FormData(form);
        try {
            const groups = await fetchGroups(formData.get("name"), groupsEndpoint);
            await updateGroups(pageId, groups);
        } finally {
            toggleSpinner(false);
            loading = false; // Re-enable the button after fetch completes
        }
    }

    function fetchGroups(name, groupsEndpoint) {
        return ApiClient.fetchData(`${groupsEndpoint}/search?name=${name}`)
            .then(async response => {
                if (response.success) {
                    return response.data;
                } else {
                    NotificationService.notify("Could not load groups", "error");
                    return [];
                }
            })
            .catch(error => {
                NotificationService.notify("An error occurred while fetching groups", "error");
                return [];
            });
    }

    async function updateGroups(pageId, groups) {
        const groupsContainer = document
            .querySelector(`[id="${pageId}"]`)
            .querySelector('.groups-container');

        for (const group of groups) {
            groupsContainer.appendChild(await GroupModule.render(group));
        }
    }

    function toggleSpinner(show) {
        const spinner = document.querySelector("#page-spinner");
        if (show) {
            spinner.classList.remove("hidden");
        } else {
            spinner.classList.add("hidden");
        }
    }

    function clearPage() {
        document.querySelector(".page")
            .querySelector(".groups-container")
            .innerHTML = '';
    }

    return {
        render: render
    };
}());

export default GroupSearchModule;
