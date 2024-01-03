import ButtonModule from "../ButtonModule.js";
import GroupModule from "../GroupModule.js";
import AddGroupForm from "../forms/AddGroupForm.js";
import ModalModule from "../ModalModule.js";

const LinkPageModule = (function () {
    async function render(pageId, groupsEndpoint, addBtn = false) {
        let page = document.createElement("div");
        page.innerHTML = `
            <section id="${pageId}" class="page-links flex-column">
                <div class="search-container flex flex-center hide-mobile">
                    <input type="text" name="search" class="input" placeholder="Search">
                </div>
                <div class="groups-container"></div>
            </section>`;
        page = page.firstElementChild;
        
        const searchContainer = page.querySelector(".search-container");
        searchContainer.appendChild(await ButtonModule.render("search", searchGroup, "btn-menu"));
        
        if (addBtn){
            searchContainer.appendChild(await ButtonModule.render("add", addGroupForm, "btn-menu"));
        }
        
        const groupsContainer = page.querySelector('.groups-container');
        for (const group of await fetchGroups(groupsEndpoint)) {
            groupsContainer.appendChild(await GroupModule.render(group));
        }

        return page;
    }

    function fetchGroups(groupsEndpoint) {
        return fetch(`http://localhost:8080/${groupsEndpoint}`)
            .then(response => response.json())
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    async function searchGroup(group, pageId) {
        const groupsContainer = document.querySelector(`[id="${pageId}"]`).querySelector('.groups-container');
        groupsContainer.appendChild(await GroupModule.render(group));
    }

    async function addGroup(group, pageId) {
        const groupsContainer = document.querySelector(`[id="${pageId}"]`).querySelector('.groups-container');
        groupsContainer.appendChild(await GroupModule.render(group));
    }
    
    async function addGroupForm(){
        document.body.appendChild(await ModalModule.render(await AddGroupForm.render()));
    }

    return {
        render: render,
        addGroup: addGroup
    };
}());

export default LinkPageModule;
