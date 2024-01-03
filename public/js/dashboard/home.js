import GroupModule from "../modules/GroupModule.js";
import ModalModule from "../modules/ModalModule.js";
import AddGroupForm from "../modules/forms/AddGroupForm.js";

export function addGroupButtons(){
    const addBtns = document.querySelectorAll(".btn-add")
    addBtns.forEach(btn => {
        btn.addEventListener("click", async () => {
            document.body.appendChild(await ModalModule.render(await AddGroupForm.render()));
        })
    })
}

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

export async function addGroup(group) {
    const groupsContainer = document.querySelector('.groups-container');
    groupsContainer.appendChild(await GroupModule.render(group))
}