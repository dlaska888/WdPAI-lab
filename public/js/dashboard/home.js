import GroupModule from "/public/js/modules/GroupModule.js";

export function fetchAllGroups() {
    fetch('http://localhost:8080/link-groups')
        .then(response => response.json())
        .then(async data => {
            const groupsContainer = document.querySelector('.groups-container');
            for (const group of data) {
                groupsContainer.appendChild(await GroupModule.render(group))
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}
